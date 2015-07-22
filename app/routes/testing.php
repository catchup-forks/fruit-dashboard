<?php

/**
 * TESTING routes
 */
if (!App::environment('production')) {

Route::group([
        'prefix'    => 'testing',
    ], function() {

    Route::get('/', array(
        'as' => 'dev.testing_page',
        'uses' => 'DevController@showTesting'
    ));

    Route::get('/stripe_load', array(
        'as' => 'dev.stripe_load',
        'uses' => 'DevController@showGetStripeData'
    ));

    Route::get('/select_personal_widgets', array(
        'as' => 'dev.select_personal_widgets',
        'uses' => 'DevController@showSelectPersonalWidgets'
    ));
});
    
}
