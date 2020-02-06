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
    return $router->app->version();
});

$router->post('/users', 'UserController@create');

$router->group(['prefix' => 'items', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/', 'ItemsController@index');
    $router->post('/', 'ItemsController@store');
    $router->get('{item}', 'ItemsController@show');
    $router->patch('{item}', 'ItemsController@update');
    $router->delete('{item}', 'ItemsController@destroy');
    $router->post('{item}/book', 'ItemsController@book');
});

$router->group(['prefix' => 'locations', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/', 'LocationsController@index');
    $router->post('/', 'LocationsController@store');
    $router->get('{location}', 'LocationsController@show');
    $router->patch('{location}', 'LocationsController@update');
    $router->delete('{location}', 'LocationsController@destroy');
});
