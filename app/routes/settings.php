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

    Route::any('disconnect/twitter', array(
        'before' => 'auth',
        'as' => 'disconnect.twitter',
        'uses' => 'SettingsController@anyDisconnectBraintree'
    ));

    Route::any('disconnect/facebook', array(
        'before' => 'auth',
        'as' => 'disconnect.facebook',
        'uses' => 'SettingsController@anyDisconnectBraintree'
    ));

    Route::any('disconnect/google', array(
        'before' => 'auth',
        'as' => 'disconnect.google',
        'uses' => 'SettingsController@anyDisconnectBraintree'
    ));

    Route::post('timezone', array(
        'as' => 'settings.timezone',
        'uses' => 'SettingsController@postTimeZone'
    ));
});
