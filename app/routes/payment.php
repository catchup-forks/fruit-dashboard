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

});

