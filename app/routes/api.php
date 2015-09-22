<?php

/**
 * Routes for API pages
 * @see APIController
 */
Route::group([
        'prefix'    => 'api',
    ], function() {

    Route::any('{api_version}/{api_key}/{widgetID}', [
        'as'     => 'api.post',
        'uses'   => 'APIController@postData'
    ]);

    Route::get('test/{widgetID}', [
        'before' => 'auth',
        'as'     => 'api.test',
        'uses'   => 'APIController@getTest'
    ]);
});
