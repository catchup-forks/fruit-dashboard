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

    Route::get('remind', [
        'as'     => 'auth.remind',
        'uses'   => 'RemindersController@getRemind'
    ]);

    Route::post('remind', [
        'as'     => 'auth.doRemind',
        'uses'   => 'RemindersController@postRemind'
    ]);

    Route::get('reset/{token}', [
        'as'     => 'auth.reset',
        'uses'   => 'RemindersController@getReset'
    ]);

    Route::post('reset', [
        'as'     => 'auth.doReset',
        'uses'   => 'RemindersController@postReset'
    ]);

    Route::any('signout', [
        'as'     => 'auth.signout',
        'uses'   => 'AuthController@anySignout'
    ]);

    Route::post('check-email', [
        'as'     => 'auth.check-email',
        'uses'   => 'AuthController@postCheckExistingEmail'
    ]);
});
