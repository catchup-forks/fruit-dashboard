<?php

/**
 * Routes for payment pages
 * @see PaymentController
 */
Route::group([
        'prefix'    => 'payment',
    ], function() {

    Route::get('/plans', array(
        'before'    => 'auth',
        'as'        => 'payment.plans',
        'uses'      => 'PaymentController@getPlansAndPricing'
    ));

    Route::post('/subscribe', array(
        'before'    => 'auth',
        'as'        => 'payment.subscribe',
        'uses'      => 'PaymentController@postSubscribe'
    ));
});

