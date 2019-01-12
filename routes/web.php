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

$router->group(['prefix' => '/global/v1'], function ($router) {
    /** country */
    $router->POST('country', 'CountryController@Create');
    $router->GET('country', 'CountryController@Read');
    $router->PUT('country', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->PATCH('country', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->DELETE('country', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->POST('country/{id}/{param}', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->GET('country/{id}', 'CountryController@ReadById');
    $router->PUT('country/{id}', 'CountryController@Replace');
    $router->PATCH('country/{id}', 'CountryController@Modify');
    $router->Delete('country/{id}', 'CountryController@Delete');
    $router->POST('country/search', 'CountryController@Search');
    /** operator **/
    $router->POST('operator', 'OperatorController@Create');
    $router->GET('operator', 'OperatorController@Read');
    $router->PUT('operator', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->PATCH('operator', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->DELETE('operator', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });

    $router->POST('operator/{id}/{param}', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->GET('operator/{id}', 'OperatorController@ReadById');
    $router->PUT('operator/{id}', 'OperatorController@Replace');
    $router->PATCH('operator/{id}', 'OperatorController@Modify');
    $router->Delete('operator/{id}', 'OperatorController@Delete');
    $router->POST('operator/search', 'OperatorController@Search');

    /** headOperator */
    $router->POST('headOperator', 'HeadOperatorController@Create');
    $router->GET('headOperator', 'HeadOperatorController@Read');
    $router->PUT('headOperator', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->PATCH('headOperator', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->DELETE('headOperator', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->POST('headOperator/{id}/{param}', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->GET('headOperator/{id}', 'HeadOperatorController@ReadById');
    $router->PUT('headOperator/{id}', 'HeadOperatorController@Replace');
    $router->PATCH('headOperator/{id}', 'HeadOperatorController@Modify');
    $router->Delete('headOperator/{id}', 'HeadOperatorController@Delete');

    /**mnvo **/
    $router->POST('mvno', 'MvnoController@Create');
    $router->GET('mvno', 'MvnoController@Read');
    $router->PUT('mvno', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->PATCH('mvno', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->DELETE('mvno', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->POST('mvno/{id}/{param}', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->GET('mvno/{id}', 'MvnoController@ReadById');
    $router->PUT('mvno/{id}', 'MvnoController@Replace');
    $router->PATCH('mvno/{id}', 'MvnoController@Modify');
    $router->Delete('mvno/{id}', 'MvnoController@Delete');

    /** mobilenetwork */
    $router->POST('mobileNetwork', 'mobileNetworkController@Create');
    $router->GET('mobileNetwork', 'mobileNetworkController@Read');
    $router->PUT('mobileNetwork', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->PATCH('mobileNetwork', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->DELETE('mobileNetwork', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->GET('mobileNetwork/{id}', 'mobileNetworkController@ReadById');

    /** currency */
    $router->POST('currency', 'CurrencyController@Create');
    $router->GET('currency', 'CurrencyController@Read');
    $router->PUT('currency', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->PATCH('currency', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->DELETE('currency', function () {
        return response()->json(['error' => "Method Not Allowed"], 405);
    });
    $router->GET('currency/{id}', 'CurrencyController@ReadById');
    $router->PUT('currency/{id}', 'CurrencyController@Replace');
    $router->PATCH('currency/{id}', 'CurrencyController@Modify');
    $router->Delete('currency/{id}', 'CurrencyController@Delete');
});
