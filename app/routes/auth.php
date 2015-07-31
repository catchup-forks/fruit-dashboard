<?php

/**
 * Routes for authentication pages
 * @see AuthController
 */
Route::group([
        'prefix'    => '',
    ], function() {

    Route::get('signin', [
        'as'     => 'auth.signin',
        'uses'   => 'AuthController@getSignin'
    ]);

    Route::post('signin', [
        'as'     => 'auth.signin',
        'uses'   => 'AuthController@postSignin'
    ]);

    Route::any('signout', [
        'as'     => 'auth.signout',
        'uses'   => 'AuthController@anySignout'
    ]);
});
