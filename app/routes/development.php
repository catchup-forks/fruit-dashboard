<?php

/**
 * Routes for development pages
 * @see TestingController
 */
if (!App::environment('production')) {

Route::group([
        'prefix'    => 'dev',
    ], function() {

    Route::get('/stripe_load', array(
        'as' => 'development.stripe_load',
        'uses' => 'DevController@showGetStripeData'
    ));

    Route::get('/select_personal_widgets', array(
        'as' => 'development.select_personal_widgets',
        'uses' => 'DevController@showSelectPersonalWidgets'
    ));
});
    
}
