<?php

/**
 * Routes for signup wizard pages
 * @see SignupWizardController
 */
Route::group([
        'prefix'    => 'signup',
    ], function() {

    Route::any('', [
        'as'     => 'signup',
        'uses'   => 'SignupWizardController@anySignup'
    ]);

    Route::get('authentication', [
        'as'     => 'signup-wizard.authentication',
        'uses'   => 'SignupWizardController@getAuthentication'
    ]);

    Route::post('authentication', [
        'as'     => 'signup-wizard.authentication',
        'uses'   => 'SignupWizardController@postAuthentication',
    ]);

    Route::any('facebook/login', array(
        'as'     => 'signup-wizard.facebook-login',
        'uses'   => 'SignupWizardController@anyFacebookLogin'
    ));

    Route::get('{step}', [
        'before' => 'auth',
        'as'     => 'signup-wizard.getStep',
        'uses'   => 'SignupWizardController@getStep'
    ]);

    Route::post('{step}', [
        'before' => 'auth',
        'as'     => 'signup-wizard.postStep',
        'uses'   => 'SignupWizardController@postStep'
    ]);
});