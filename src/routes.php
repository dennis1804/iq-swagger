<?php


Route::group(['middleware' => ['web', 'auth']], function ($router) {

Route::get( '/api', [
        'as' => 'iq-swagger.api',
        'uses' => 'Dennis1804\IqSwagger\ApiDocController@getDoc'
    ]);



	Route::get( '/swagger', [
        'as' => 'iq-swagger.swagger',
        'uses' => 'Dennis1804\IqSwagger\ApiDocController@getSwagger'
    ]);

});