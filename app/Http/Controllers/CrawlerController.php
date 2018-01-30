<?php

namespace App\Http\Controllers;

use App\Skill;
use App\StopWord;
use GuzzleHttp;
use Illuminate\Http\Request;
use Validator;

class CrawlerController extends Controller
{
    function filterResult($data)
    {
        $result = Skill::select('title')
            ->get()
            ->toArray();

        $skills = array_column($result, 'title');
        $skills = array_unique($skills);
        asort($skills);

        $result = StopWord::select('title')
            ->get()
            ->toArray();

        $stopWords = array_column($result, 'title');
        $stopWords = array_unique($stopWords);
        asort($stopWords);


        foreach ($data as $node)
        {
            $words = array_column($node->connects, 'subSkill');
            $words = array_unique($words);
            asort($words);

            foreach (array_intersect($words, $skills) as $key => $word)
                $node->connects[$key]->type = 'skill';


            foreach (array_intersect($words, $stopWords) as $key => $word)
                $node->connects[$key]->type = 'stop word';
        }

        return $data;
    }

    function callAPI($idCrawler, $page)
    {
        $client = new GuzzleHttp\Client();

        $route = 'http://localhost:3001/api/extractor/graphskill'.'?crawler_id=' . $idCrawler . '&page=' . $page;
        $res = $client->get($route);

        return json_decode($res->getBody());
    }

    function getGraphSkill(Request $request)
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
            return response()->json($errors->all());
        }




    }

    public function validator($request)
    {
        $rules =  array(
            'page' => 'nullable|integer',
            'crawler_id' => 'required|integer'
        );

        return \Validator::make([
            'page' => $request->input('page'),
            'crawler_id' => $request->input('crawler_id')
        ], $rules);
    }
}