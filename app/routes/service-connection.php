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
        'before' => 'auth',
        'as'     => 'service.braintree.connect',
        'uses'   => 'ServiceConnectionController@getBraintreeConnect'
    ]);

    Route::post('braintree/connect', [
        'before' => 'auth',
        'as'     => 'service.braintree.connect',
        'uses'   => 'ServiceConnectionController@postBraintreeConnect'
    ]);

    Route::any('braintree/disconnect', array(
        'before' => 'auth',
        'as' => 'service.braintree.disconnect',
        'uses' => 'ServiceConnectionController@anyBraintreeDisconnect'
    ));

    /* -- Stripe -- */
    Route::any('stripe/connect', [
        'before' => 'auth',
        'as'     => 'service.stripe.connect',
        'uses'   => 'ServiceConnectionController@anyStripeConnect'
    ]);

    Route::any('stripe/disconnect', array(
        'before' => 'auth',
        'as' => 'service.stripe.disconnect',
        'uses' => 'ServiceConnectionController@anyStripeDisconnect'
    ));

    /* -- Twitter -- */
    Route::any('twitter/connect', [
        'before' => 'auth',
        'as'     => 'service.twitter.connect',
        'uses'   => 'ServiceConnectionController@anyTwitterConnect'
    ]);

    Route::any('twitter/disconnect', [
        'before' => 'auth',
        'as'     => 'service.twitter.disconnect',
        'uses'   => 'ServiceConnectionController@anyTwitterDisconnect'
    ]);

    /* -- Google -- */
    Route::any('google_analytics/connect', [
        'before' => 'auth',
        'as'     => 'service.google_analytics.connect',
        'uses'   => 'ServiceConnectionController@anyGoogleAnalyticsConnect'
    ]);
    Route::any('google_analytics/disconnect', [
        'before' => 'auth',
        'as'     => 'service.google_analytics.disconnect',
        'uses'   => 'ServiceConnectionController@anyGoogleAnalyticsDisconnect'
    ]);

    Route::any('google_analytics/refresh-properties', [
        'before' => 'auth',
        'as'     => 'service.google_analytics.refresh-properties',
        'uses'   => 'ServiceConnectionController@anyGoogleAnalyticsRefreshProperties'
    ]);

    Route::get('google_analytics/select-properties', array(
        'before' => 'auth',
        'as'     => 'service.google_analytics.select-properties',
        'uses'   => 'ServiceConnectionController@getSelectGoogleAnalyticsProperties'
    ));

    Route::post('google_analytics/select-properties', array(
        'before' => 'auth',
        'as'     => 'service.google_analytics.select-properties',
        'uses'   => 'ServiceConnectionController@postSelectGoogleAnalyticsProperties'
    ));


    /* -- Facebook -- */
    Route::any('facebook/connect', array(
        'before' => 'auth',
        'as'     => 'service.facebook.connect',
        'uses'   => 'ServiceConnectionController@anyFacebookConnect'
    ));

    Route::any('facebook/disconnect', array(
        'before' => 'auth',
        'as'     => 'service.facebook.disconnect',
        'uses'   => 'ServiceConnectionController@anyFacebookDisconnect'
    ));

    Route::get('facebook/select-pages', array(
        'before' => 'auth',
        'as'     => 'service.facebook.select-pages',
        'uses'   => 'ServiceConnectionController@getSelectFacebookPages'
    ));

    Route::post('facebook/select-pages', array(
        'before' => 'auth',
        'as'     => 'service.facebook.select-pages',
        'uses'   => 'ServiceConnectionController@postSelectFacebookPages'
    ));

    Route::any('facebook/refresh-pages', [
        'before' => 'auth',
        'as'     => 'service.facebook.refresh-pages',
        'uses'   => 'ServiceConnectionController@anyFacebookRefreshPages'
    ]);

});