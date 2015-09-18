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

    Route::any('financial-connections', [
        'before' => 'auth',
        'as'     => 'signup-wizard.financial-connections',
        'uses'   => 'SignupWizardController@anyFinancialConnections'
    ]);

    Route::any('social-connections', [
        'before' => 'auth',
        'as'     => 'signup-wizard.social-connections',
        'uses'   => 'SignupWizardController@anySocialConnections'
    ]);

    Route::any('web-analytics-connections', [
        'before' => 'auth',
        'as'     => 'signup-wizard.web-analytics-connections',
        'uses'   => 'SignupWizardController@anyWebAnalyticsConnections'
    ]);

    Route::any('personal-widgets', [
        'before' => 'auth',
        'as'     => 'signup-wizard.personal-widgets',
        'uses'   => 'SignupWizardController@anyPersonalWidgets'
    ]);

    Route::any('facebook/login', array(
        'as'     => 'signup-wizard.facebook-login',
        'uses'   => 'SignupWizardController@anyFacebookLogin'
    ));

});