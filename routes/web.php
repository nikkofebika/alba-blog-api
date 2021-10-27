<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
	return $router->app->version();
});

$router->post('register', 'AuthController@register');
$router->post('login', 'AuthController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
	$router->get('users[/{id}]', 'UserController@index');

	$router->group(['prefix' => 'categories'], function () use ($router) {
		$router->get('/', 'CategoryController@index');
		$router->get('{id}', 'CategoryController@index');
		$router->post('/', 'CategoryController@store');
		$router->put('{id}', 'CategoryController@update');
		$router->delete('{id}', 'CategoryController@destroy');
	});

	$router->group(['prefix' => 'tags'], function () use ($router) {
		$router->get('/', 'TagController@index');
		$router->get('{id}', 'TagController@index');
		$router->post('/', 'TagController@store');
		$router->put('{id}', 'TagController@update');
		$router->delete('{id}', 'TagController@destroy');
	});

	$router->group(['prefix' => 'posts'], function () use ($router) {
		$router->get('/', 'PostController@index');
		$router->get('{id}', 'PostController@index');
		$router->post('/', 'PostController@store');
		$router->put('{id}', 'PostController@update');
		$router->delete('{id}', 'PostController@destroy');
	});
});