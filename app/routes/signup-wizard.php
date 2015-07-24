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

    Route::get('personal-widgets', [
        'as'     => 'signup-wizard.personal-widgets',
        'uses'   => 'SignupWizardController@getPersonalWidgets'
    ]);

    Route::post('personal-widgets', [
        'as'     => 'signup-wizard.personal-widgets',
        'uses'   => 'SignupWizardController@postPersonalWidgets'
    ]);

    Route::get('financial-connections', [
        'as'     => 'signup-wizard.financial-connections',
        'uses'   => 'SignupWizardController@getFinancialConnections'
    ]);

    Route::post('financial-connections', [
        'as'     => 'signup-wizard.financial-connections',
        'uses'   => 'SignupWizardController@postFinancialConnections'
    ]);

    /* -- Braintree -- */
    /* For development only, probably will go to modal,
     * and be handled by financial-connections.
    */
    Route::get('braintree/connect', [
        'as'     => 'signup-wizard.braintree-connect',
        'uses'   => 'SignupWizardController@getBraintreeConnect'
    ]);

    Route::post('braintree/connect', [
        'as'     => 'signup-wizard.braintree-connect',
        'uses'   => 'SignupWizardController@postBraintreeConnect'
    ]);
});