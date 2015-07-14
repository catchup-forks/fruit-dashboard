<?php

Route::get('/', function()
{
    return Redirect::route('dashboard.dashboard');
});

Route::get('/testing', array(
    'as' => 'dev.testing_page',
    'uses' => 'DevController@showTesting'
));
