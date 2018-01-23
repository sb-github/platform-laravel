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
	
});