<?php

Route::get('/', function()
{
    return Redirect::route('dev.testing_page');
});

Route::get('/testing', array(
    'as' => 'dev.testing_page',
    'uses' => 'DevController@showTesting'
));
Route::get('/stripe_load', array(
    'as' => 'dev.stripe_load',
    'uses' => 'DevController@showGetStripeData'
));
