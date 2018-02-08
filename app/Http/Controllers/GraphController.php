<?php

namespace App\Http\Controllers;

use App\GraphSkill;
use App\Skill;
use App\StopWord;
use GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use Validator;

class GraphController extends Controller
{
    function prepareArray($array, $key)
    {
        $result = array_column($array, $key);
        $result = array_unique($result);
        asort($result);

        return $result;
    }

    function filterResult($data)
    {
        $result = Skill::select('title')
            ->get()
            ->toArray();
        $skills = $this->prepareArray($result, 'title');

        $result = StopWord::select('title')
            ->get()
            ->toArray();
        $stopWords = $this->prepareArray($result, 'title');

        foreach ($data as $node)
        {
            $words = $this->prepareArray($node->connects, 'subSkill');
            $node->tag = count(array_intersect([$node->skill], $skills)) == 0
                ? count(array_intersect([$node->skill], $stopWords)) == 0
                    ? 'new'
                    : 'stopword'
                : 'skill';
            $count = 0;

            foreach (array_diff($words, $skills) as $key => $word)
                $node->connects[$key]->tag = 'new';

            foreach (array_diff($words, $stopWords) as $key => $word)
                $node->connects[$key]->tag = 'new';

            foreach (array_intersect($words, $skills) as $key => $word) {
                $node->connects[$key]->tag = 'skill';
                $count++;
            }

            foreach (array_intersect($words, $stopWords) as $key => $word) {
                $node->connects[$key]->tag = 'stopword';
                $count++;
            }

            $node->new = count($node->connects) - $count;
        }

        return $data;
    }

    function callAPI($idCrawler, $page)
    {
        $client = new GuzzleHttp\Client();
        $route = env('EXTRACTOR_API') . env('GRAPH_API')
            . '?crawler_id=' . $idCrawler . '&page=' . $page;

        $res = $client->get($route);

        return json_decode($res->getBody());
    }

    function getCrawlerResult(Request $request)
    {
        $validator = $this->validator($request);

        if(!$validator->fails()) {
            $crawlerId = $request->input('crawler_id');
            $page = $request->input('page');
            $page = isset($page) ? $page : 1;

            $result = $this->filterResult($this->callAPI($crawlerId, $page));

            return response()->json($result);
        } else {
            $errors = $validator->errors();
            return response([
                'status' => 'error',
                'message' => $errors->all()[0]
            ],400);
        }
    }

    function createNode($skillId)
    {
        $skill = Skill::select('id', 'title as label', 'image')
            ->where('id', $skillId)
            ->first();

        if($skill){
            $skillWeight = DB::table('graph_skill as gs')
                ->join('graph_skill_vacancies as gsv', 'gs.id', '=', 'gsv.graph_skill_id')
                ->select(DB::raw('COUNT(DISTINCT gsv.vacancy_id) as weight'))
                ->where('gs.parent_skill', $skillId)
                ->orWhere('gs.related_skill', $skillId)
                ->get()->toArray()[0]->weight;

            $node = $skill->toArray();
            $node['value'] = $skillWeight;

            return $node;
        }

        return false;
    }

    function createEdge($relation, $skillId)
    {
        $edge = [];

        $edge['id'] = $relation->id;
        $edge['from'] = $skillId;
        $edge['to'] = $relation->parent_skill != $skillId
            ? $relation->parent_skill
            : $relation->related_skill;
        $edge['value'] = $relation->weight;

        return $edge;
    }

    function skillExists($id, $skill)
    {
        if(isset($id) || isset($skill)){

            $result = Skill::select('id')
                ->where('title', $skill)
                ->orWhere('id', $id)
                ->first();

            if($result)
                return $result->toArray()['id'];
        }

        return false;
    }

    function getGraphSkill(Request $request)
    {
        $skill = $this->skillExists(
            $request->input('skill_id'),
            $request->input('skill')
        );

        if($skill)
        {
            $graph = [];

            $relations = GraphSkill::select('id', 'parent_skill', 'related_skill', 'weight')
                ->where('parent_skill', $skill)
                ->orWhere('related_skill', $skill)
                ->get()->toJson();
            $node = $this->createNode($skill);
            if($node)
                $graph['nodes'][] = $node;

            foreach (json_decode($relations) as $relation)
            {
                $edge = $this->createEdge($relation, $skill);
                $node = $this->createNode($edge['to']);

                $graph['edges'][] = $edge;
                if($node)
                    $graph['nodes'][] = $node;
            }

            return response()->json($graph);
        }

        return response([
            'status' => 'error',
            'message' => 'Skill not found'
        ],400);
    }

    public function validator($request)
    {
        $rules =  array(
            'page' => 'nullable|integer',
            'crawler_id' => 'required'
        );

        return \Validator::make([
            'page' => $request->input('page'),
            'crawler_id' => $request->input('crawler_id')
        ], $rules);
    }

}