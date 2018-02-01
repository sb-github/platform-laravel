<?php

namespace App\Http\Controllers;

use App\GraphSkill;
use App\GraphSkillVacancy;
use App\Skill;
use App\Vacancy;
use GuzzleHttp;
use Illuminate\Http\Request;


class MergeController extends Controller
{
    public function __construct()
    {

    }

    public function skills($requestedSkill, $type)
    {
        if(strcmp($requestedSkill,"all") == 0)
        {
            $result = Skill::select('title')
                ->get()
                ->toJson();
        }
        else
        {
            $idRequestedSkill = Skill::select('id')
                ->where('title', $requestedSkill)
                ->first()
                ->toArray()['id'];

            if(strcmp($type,"update") == 0)
            {
                $relatedSkills = GraphSkill::select('parent_skill', 'related_skill')
                    ->where('parent_skill', $idRequestedSkill)
                    ->orWhere('related_skill', $idRequestedSkill)
                    ->get()
                    ->toArray();

                $idRelatedSkills = [];
                foreach ($relatedSkills as $item)
                {
                    $idRelatedSkills[] = $item['parent_skill'];
                    $idRelatedSkills[] = $item['related_skill'];
                }

                $result = Skill::select('title')
                    ->whereIn('id', array_unique($idRelatedSkills))
                    ->get()
                    ->toJson();
            }
            else
            {
                $result = Skill::select('title')
                    ->get()
                    ->toJson();
            }
        }

        $skills = [];

        foreach (json_decode($result) as $item)
            $skills[] = $item->title;

        return $skills;
    }
    public function merge(Request $request, $requestedSkill)
    {
        $start = microtime(true);

        $skills = $this->skills($requestedSkill,'search');
        $crawler = $request->input('crawler_id');

        $result = $this->callAPI($crawler, $requestedSkill, $skills);

        foreach ($result as $skill)
            $this->graphMerger($skill);

        return response()->json(['time' => microtime(true) - $start]);
    }
    public function callAPI($crawler,$requestedSkill, $skills)
    {
        $client = new GuzzleHttp\Client();
        $route = env('EXTRACTOR_API') . env('MERGE_GRAPH_API') . $requestedSkill;

        $res = $client->post($route, [
            GuzzleHttp\RequestOptions::JSON => [
                'crawler_id' => $crawler,
                'skills' => $skills
            ]
        ]);

        return json_decode($res->getBody());
    }
    public function graphMerger($skill)
    {
        $parentSkill = Skill::select('id')
            ->where('title', $skill->skill)
            ->first()
            ->toArray()['id'];

        foreach ($skill->connects as $key => $relatedSkill)
        {
            $relation = $this->relationMerger($parentSkill, $relatedSkill, $skill->date);

            $vacancies = $this->linksMerger($relatedSkill->links);

            $this->vacanciesMerger($vacancies, $relation->id, $relation->last_date);
        }

    }
    public function relationMerger($parentSkill, $relatedSkill, $lastDate)
    {
        $relatedSkill->id = Skill::select('id')
            ->where('title', $relatedSkill->subskill)
            ->first()
            ->toArray()['id'];

        $graphRelation = GraphSkill::select('id', 'last_date')
            ->where([
                ['parent_skill', $parentSkill],
                ['related_skill', $relatedSkill->id]
            ])
            ->orWhere([
                ['parent_skill', $relatedSkill->id],
                ['related_skill', $parentSkill]
            ])
            ->first();

        if(!isset($graphRelation))
        {
            GraphSkill::insert([
                'parent_skill' => $parentSkill,
                'related_skill' => $relatedSkill->id,
                'weight' => $relatedSkill->weight,
                'last_date' => date('Y-m-d H:i:s', $lastDate)
            ]);

            $graphRelation = GraphSkill::select('id', 'last_date')
                ->where([
                    ['parent_skill', $parentSkill],
                    ['related_skill', $relatedSkill->id]
                ])
                ->orWhere([
                    ['parent_skill', $relatedSkill->id],
                    ['related_skill', $parentSkill]
                ])
                ->first();
        }
        elseif(date('Y-m-d H:i:s', $lastDate) > $graphRelation->last_date)
        {
            $graphRelation->last_date = date('Y-m-d H:i:s', $lastDate);
            $graphRelation->weight = $relatedSkill->weight;
            $graphRelation->save();
        }

        $graphRelation = json_decode($graphRelation->toJson());

        return $graphRelation;
    }
    public function linksMerger($links)
    {
        $links = array_unique($links);

        $result = Vacancy::select('id','link')
            ->whereIn('link', $links)
            ->get()->toJson();

        $oldLinks = [];
        $linksId = [];

        foreach (json_decode($result) as $item)
        {
            $oldLinks[] = $item->link;
            $linksId[] = $item->id;
        }

        foreach (array_diff($links, $oldLinks) as $item)
        {
            Vacancy::insert([
                'link' => $item
            ]);

            $linksId[] = Vacancy::select('id')
                ->where('link', $item)
                ->first()
                ->toArray()['id'];
        }

        return $linksId;
    }
    public function vacanciesMerger($vacancies, $graphSkill, $lastDate)
    {
        $result = GraphSkillVacancy::select('vacancy_id')
            ->where('graph_skill_id', $graphSkill)
            ->get()
            ->toJson();

        $oldVacancies = [];

        foreach (json_decode($result) as $item)
            $oldVacancies[] = $item->vacancy_id;

        foreach (array_diff($vacancies, $oldVacancies) as $item)
            GraphSkillVacancy::insert([
                'graph_skill_id' => $graphSkill,
                'vacancy_id' => $item,
                'last_date' => date('Y-m-d H:i:s', strtotime($lastDate))
            ]);

        GraphSkillVacancy::whereIn('vacancy_id', $oldVacancies)
            ->where([
                ['graph_skill_id', $graphSkill],
                ['last_date', '<', date('Y-m-d H:i:s', strtotime($lastDate))]
            ])
            ->update([
                'last_date' => date('Y-m-d H:i:s', strtotime($lastDate))
            ]);
    }
}
