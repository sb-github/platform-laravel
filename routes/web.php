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


	
	//--=API DIR START=--
    $router->get('material', 'MaterialController@get');
    $router->post('material', 'MaterialController@create');
	
	$router->get('material/{id}', 'MaterialController@getspecific');
	$router->put('material/{id}', 'MaterialController@update');
	
	$router->get('skill/{id}/materials', 'MaterialController@getBySkill');
	
	$router->delete('material/{id}', 'MaterialController@delete');
	//---=API DIR END=---

