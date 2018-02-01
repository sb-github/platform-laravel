<?php

namespace App\Http\Controllers;

use App\Skill;
use App\StopWord;
use GuzzleHttp;
use Illuminate\Http\Request;
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
            'crawler_id' => 'required'
        );

        return \Validator::make([
            'page' => $request->input('page'),
            'crawler_id' => $request->input('crawler_id')
        ], $rules);
    }

}