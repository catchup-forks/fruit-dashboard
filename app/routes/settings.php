<?php

/**
 * Routes for settings pages
 * @see SettingsController
 */
Route::group([
        'prefix'    => 'settings',
    ], function() {

    Route::any('', array(
        'before' => 'auth',
        'as' => 'settings.settings',
        'uses' => 'SettingsController@anySettings'
    ));

    Route::post('change/{attrName}', array(
        'before' => 'auth',
        'as' => 'settings.change',
        'uses' => 'SettingsController@postSettingsChange'
    ));

    Route::post('timezone', array(
        'as' => 'settings.timezone',
        'uses' => 'SettingsController@postTimeZone'
    ));
});
