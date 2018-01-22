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
    
});
$router->get('/skills', 'SkillController@all');
$router->post('/skills', 'SkillController@create');
$router->put('/skills/{id}','SkillController@update');
$router->get('/skills/{id}','SkillController@showone');
$router->delete('/skills/{id}','SkillController@delete');