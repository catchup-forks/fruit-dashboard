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
    Route::any('stripe/connect', [
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
        'as'     => 'service.twitter.connect',
        'uses'   => 'ServiceConnectionController@anyTwitterConnect'
    ]);

    Route::any('twitter/disconnect', [
        'as'     => 'service.twitter.disconnect',
        'uses'   => 'ServiceConnectionController@anyTwitterDisconnect'
    ]);

    /* -- Google -- */
    Route::any('google_analytics/connect', [
        'as'     => 'service.google_analytics.connect',
        'uses'   => 'ServiceConnectionController@anyGoogleAnalyticsConnect'
    ]);
    Route::any('google_analytics/disconnect', [
        'as'     => 'service.google_analytics.disconnect',
        'uses'   => 'ServiceConnectionController@anyGoogleAnalyticsDisconnect'
    ]);

    Route::any('google_calendar/connect', [
        'as'     => 'service.google_calendar.connect',
        'uses'   => 'ServiceConnectionController@anyGoogleCalendarConnect'
    ]);
    Route::any('google_calendar/disconnect', [
        'as'     => 'service.google_calendar.disconnect',
        'uses'   => 'ServiceConnectionController@anyGoogleCalendarDisconnect'
    ]);

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

});