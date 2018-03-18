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

$router->get('create', 'ListsController@createList');
$router->get('additem', 'ListsController@addItemToList');
$router->get('checkoff', 'ListsController@checkOffList');
$router->get('getlist', 'ListsController@showList');
$router->get('getlistdata','ListsController@showListData');
$router->get('deletelistitem','ListsController@removeItemFromList');
$router->get('updateitem','ListsController@updateListItem');