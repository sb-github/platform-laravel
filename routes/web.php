<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {

    return 'API v1';
});
$router->group(['prefix' => 'api/v1'], function () use ($router) {

	  //--=Merge skill API=--
    $router->post('/merge/{requestedSkill}','MergeController@merge');
    //--=Merge skill API=--

    //--=API MATERIALS START=--
    $router->get('material', 'MaterialController@get');
    $router->post('material', 'MaterialController@create');
	  $router->get('material/{id}', 'MaterialController@getspecific');
	  $router->put('material/{id}', 'MaterialController@update');
	  $router->get('skill/{id}/materials', 'MaterialController@getBySkill');
	  $router->delete('material/{id}', 'MaterialController@delete');
	  //---=API MATERIALS END=---
	
    //--=API DIR START=--
    $router->get('directions', 'DirectionController@get');
    $router->post('directions', 'DirectionController@create');
    $router->get('directions/{id}', 'DirectionController@getspecific');
    $router->put('directions/{id}', 'DirectionController@update');
    $router->get('directions/{id}/subdirections', 'DirectionController@subdir');
    $router->post('directions/{id}/subdirections', 'DirectionController@addsubdir');
    $router->delete('directions/{id}', 'DirectionController@delete');
    //---=API DIR END=---

    //--=API SKILL START=--
    $router->get('/skills', 'SkillController@all');
    $router->post('/skill', 'SkillController@create');
    $router->put('/skills/{id}','SkillController@update');
    $router->get('/skills/{id}','SkillController@showone');
    $router->delete('/skills/{id}','SkillController@delete');
    $router->get('/directions/{id}/skill','SkillController@dir');
    $router->post('/directions/{id}/skill','SkillController@addSkillAndDir');
    $router->post('/directions/{id}/skill/{skillId}','SkillController@addtodir');
    //---=API SKILLS END=---

    //---=API STOP_WORDS START=---
    $router->get('/stopword', 'StopWordController@all');
    $router->get('/stopword/{id}','StopWordController@showone');
    $router->get('/crawler/{id}/stopword','StopWordController@showByCrawler');
    $router->post('/stopword', 'StopWordController@create');
    $router->put('/stopword/{id}','StopWordController@update');
    $router->delete('/stopword','StopWordController@del_all');
    $router->delete('/stopword/{id}','StopWordController@delete');
    $router->delete('/crawler/{id}/stopword','StopWordController@deleteByCrawler');
           
    //---=API STOP_WORDS END=--- 
    $router->post('/skills', 'SkillController@create_array');
    //---=API SKILLS END=---
    $router->get('/graphskill', 'GraphController@getGraphSkill');
});
