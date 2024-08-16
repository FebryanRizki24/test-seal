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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('login', 'AuthController@login');
        $router->post('register', 'AuthController@register');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');
        $router->post('user-profile', 'AuthController@me');
    });

    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->group(['prefix' => 'user'], function () use ($router) {
            $router->post('/store', 'UserController@store');
            $router->get('/index', 'UserController@index');
            $router->get('/getTaskUser', 'UserController@getTaskUser');
            $router->put('/update/{id}', 'UserController@update');
            $router->post('/updateAvatar/{id}', 'UserController@updateAvatar');
            $router->delete('/destroy/{id}', 'UserController@destroy');
        });

        $router->group(['prefix' => 'task'], function () use ($router) {
            $router->post('/store', 'TaskController@store');
            $router->get('/index', 'TaskController@index');
            $router->put('/update/{id}', 'TaskController@update');
            $router->delete('/destroy/{id}', 'TaskController@destroy');
        });

        $router->group(['prefix' => 'project'], function () use ($router) {
            $router->post('/store', 'ProjectController@store');
            $router->get('/index', 'ProjectController@index');
            $router->get('/getProjectAndTask', 'ProjectController@getProjectAndTask');
            $router->put('/update/{id}', 'ProjectController@update');
            $router->delete('/destroy/{id}', 'ProjectController@destroy');
        });
    });
});
