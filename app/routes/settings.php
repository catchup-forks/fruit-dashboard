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

    Route::any('disconnect/stripe', array(
        'before' => 'auth',
        'as' => 'disconnect.stripe',
        'uses' => 'SettingsController@anyDisconnectStripe'
    ));

    Route::any('disconnect/braintree', array(
        'before' => 'auth',
        'as' => 'disconnect.braintree',
        'uses' => 'SettingsController@anyDisconnectBraintree'
    ));

    Route::post('timezone', array(
        'as' => 'settings.timezone',
        'uses' => 'SettingsController@postTimeZone'
    ));
});
