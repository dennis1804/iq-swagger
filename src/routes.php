<?php
Route::get( '/api', [
        'as' => 'iq-swagger.api',
        'uses' => 'ReinderEU\IqSwagger\ApiDocController@getDoc'
    ]);


Route::get( '/swagger', [
        'as' => 'iq-swagger.swagger',
        'uses' => 'ReinderEU\IqSwagger\ApiDocController@getSwagger'
    ]);