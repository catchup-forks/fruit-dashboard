<?php

/**
 * Routes for payment pages
 * @see PaymentController
 */
Route::group([
        'prefix'    => 'payment',
    ], function() {

    Route::get('plans', [
        'before'    => 'auth',
        'as'        => 'payment.plans',
        'uses'      => 'PaymentController@getPlans'
    ]);

    Route::get('subscribe/{planID}', [
        'before'    => 'auth',
        'as'        => 'payment.subscribe',
        'uses'      => 'PaymentController@getSubscribe'
    ]);

    Route::post('subscribe/{planID}', [
        'before'    => 'auth',
        'as'        => 'payment.subscribe',
        'uses'      => 'PaymentController@postSubscribe'
    ]);

    Route::get('unsubscribe', [
        'before'    => 'auth',
        'as'        => 'payment.unsubscribe',
        'uses'      => 'PaymentController@getUnsubscribe'
    ]);

    Route::post('unsubscribe', [
        'before'    => 'auth',
        'as'        => 'payment.sunubscribe',
        'uses'      => 'PaymentController@postUnsubscribe'
    ]);

});

