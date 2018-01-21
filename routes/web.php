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
    $router->get('direction', 'DirectionController@get');
    $router->post('direction', 'DirectionController@create');
	
	$router->get('direction/{id}', 'DirectionController@getspecific');
	$router->put('direction/{id}', 'DirectionController@update');
	
	$router->delete('direction/{id}', 'DirectionController@delete');
	//---=API DIR END=---
	
});