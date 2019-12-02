<?php
Route::get( '/api', [
        'as' => 'iq-swagger.api',
        'uses' => 'Dennis1804\IqSwagger\ApiDocController@getDoc'
    ]);



Route::group(['middleware' => 'cors'], function ($router) {
	Route::get( '/swagger', [
        'as' => 'iq-swagger.swagger',
        'uses' => 'Dennis1804\IqSwagger\ApiDocController@getSwagger'
    ]);

});