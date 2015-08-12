<?php

/**
 * Routes for service connection pages
 * @see ServiceConnectionController
 */
Route::group([
        'prefix'    => 'service',
    ], function() {

    /* -- Braintree -- */
    Route::get('braintree/connect', [
        'as'     => 'service.braintree.connect',
        'uses'   => 'ServiceConnectionController@getBraintreeConnect'
    ]);

    Route::post('braintree/connect', [
        'as'     => 'service.braintree.connect',
        'uses'   => 'ServiceConnectionController@postBraintreeConnect'
    ]);

    Route::any('braintree/disconnect', array(
        'before' => 'auth',
        'as' => 'service.braintree.disconnect',
        'uses' => 'ServiceConnectionController@anyBraintreeDisconnect'
    ));

    /* -- Stripe -- */
    Route::any('stripe/disconnect', array(
        'before' => 'auth',
        'as' => 'service.stripe.disconnect',
        'uses' => 'ServiceConnectionController@anyStripeDisconnect'
    ));

    /* -- Twitter -- */
    Route::any('twitter/connect', [
        'as'     => 'service.twitter.connect',
        'uses'   => 'ServiceConnectionController@anyTwitterConnect'
    ]);
    Route::any('twitter/disconnect', [
        'as'     => 'service.twitter.disconnect',
        'uses'   => 'ServiceConnectionController@anyTwitterDisconnect'
    ]);

    /* -- Facebook -- */
    Route::any('facebook/disconnect', array(
        'before' => 'auth',
        'as' => 'service.facebook.disconnect',
        'uses' => 'SettingsController@anyDisconnectBraintree'
    ));

    Route::any('google/disconnect', array(
        'before' => 'auth',
        'as' => 'service`.google.disconnect',
        'uses' => 'SettingsController@anyDisconnectBraintree'
    ));

});