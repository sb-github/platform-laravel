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
    $router->post('/skills', 'SkillController@create');
    $router->put('/skills/{id}','SkillController@update');
    $router->get('/skills/{id}','SkillController@showone');
    $router->delete('/skills/{id}','SkillController@delete');
    $router->get('/directions/{id}/skills','SkillController@dir');
    $router->post('/directions/{id}/skills/{skillId}','SkillController@addtodir');
        //---=API SKILLS END=---
});