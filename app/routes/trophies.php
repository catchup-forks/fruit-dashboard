<?php

/**
 * Routes for trophies page
 * @see TrophiesController
 */
Route::group([
        'prefix'    => 'trophies',
    ], function() {

    Route::any('', array(
        'before' => 'auth',
        'as' => 'trophies.trophies',
        'uses' => 'TrophiesController@anyTrophies'
    ));
});