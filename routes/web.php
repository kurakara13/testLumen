<?php
use Illuminate\Http\Request;
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

$router->post('/login', 'UserController@login');

// checklists template
$router->get('/checklists/templates', 'CheckListTemplateController@showAll');
$router->get('/checklists/templates/{id}', 'ChecklistTemplateController@show');
$router->post('/checklists/templates', 'ChecklistTemplateController@create');
$router->patch('/checklists/templates/{id}', 'ChecklistTemplateController@update');
$router->delete('/checklists/templates/{id}', 'ChecklistTemplateController@delete');

// checklists
$router->get('/checklists', 'ChecklistController@showAll');
$router->get('/checklists/{id}', 'ChecklistController@show');
$router->post('/checklists', 'ChecklistController@create');
$router->patch('/checklists/{id}', 'ChecklistController@update');
$router->delete('/checklists/{id}', 'ChecklistController@delete');

// checklists item
$router->get('/checklists/{checklistId}/items', 'ChecklistItemsController@showAll');
$router->get('/checklists/{checklistId}/items/{itemId}', 'ChecklistItemsController@show');
$router->post('/checklists/{checklistId}/items', 'ChecklistItemsController@create');
$router->patch('/checklists/{checklistId}/items/{itemId}', 'ChecklistItemsController@update');
$router->post('/checklists/{checklistId}/items/_bulk', 'ChecklistItemsController@update_bulk');
$router->delete('/checklists/{checklistId}/items/{itemId}', 'ChecklistItemsController@delete');
$router->post('/checklists/complete', 'ChecklistItemsController@complete');
$router->post('/checklists/incomplete', 'ChecklistItemsController@incomplete');
$router->get('/checklists/items/summaries', 'ChecklistItemsController@summaries');
