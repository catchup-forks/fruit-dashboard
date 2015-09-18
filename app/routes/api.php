<?php

/**
 * Routes for API pages
 * @see APIController
 */
Route::group([
        'prefix'    => 'api',
    ], function() {

    Route::any('{api_version}/{api_key}/{widgetID}', [
        'as'     => 'api.save-data',
        'uses'   => 'APIController@anySaveData'
    ]);

    Route::any('example', [
        'before' => 'auth',
        'as'     => 'api.example',
        'uses'   => 'APIController@anyExample'
    ]);
});
