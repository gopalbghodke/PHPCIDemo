<?php

/**
 * ROUTES STANDARDS
 * To work efficently with the application, we define some routes methods that must be applied.
 * For reference, https://jsonapi.org/format/ will be followed to insure uniformity of the whole application communication.
 *
 * REST CRUD operations (application/vnd.api+json):
 *     - POST: "Create"
 *     - GET: "Read"
 *     - PATCH: "Update"
 *     - DELETE: "Delete"
 *
 * Any other non-REST actions:
 *     - PUT: "anything"
 *
 */

/**
 * ROUTE FACADE
 * Because Route is a facade, the "\" (backslash) in \Route is a good practice to always add to avoid a namespace issue in few cases
 *
 */

/**
 * ROUTE CACHE
 * 1) Because of cache process of Laravel, don't define routes outside of the method "loadRoutesFrom" (cf: https://laravel.com/docs/6.x/packages#routes)
 * 2) Do not use closure instead, this won't make cache working, must be defined by a string like " \Route::put('test', 'SampleController@putTest'); ".
 *
 */

\Route::group([
    'middleware' => ['api',],
    'prefix' => 'brunoocto/sample',
    'namespace' => 'Brunoocto\Sample\Controllers',
], function () {
    \Route::group([
        // Define the group of the route for the resource Sample (can be GET, POST, PAtCH, DELETE), use plural "samples" (usually equals to the table name)
        'prefix' => 'samples',
    ], function () {
        // Model Sample
        \Route::post('/', 'SampleController@postSample');
    });

    // Divers actions
    \Route::put('dependency-injection', 'SampleController@putDependency');
    \Route::put('interface', 'SampleController@putInterface');
    \Route::put('facade', 'SampleController@putFacade');
    \Route::put('maker', 'SampleController@putMaker');
    \Route::put('test', 'SampleController@putTest');
});

/**
 * Map VND resources routes
 * Make sure that namespace and folder architecture respect PSR-4 standard.
 *
 */
\Route::group([
    'middleware' => ['api',],
    'prefix' => 'brunoocto/sample/pm',
], function () {
    \LinckoVmodel::mapConfigResources(__DIR__.'/../../config/json-api.resources.php');
});
