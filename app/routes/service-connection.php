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

    /* -- Twitter -- */
    Route::any('twitter/connect', [
        'as'     => 'service.twitter.connect',
        'uses'   => 'ServiceConnectionController@anyTwitterConnect'
    ]);

});