<?php

/**
 * Routes for signup wizard pages
 * @see SignupWizardController
 */
Route::group([
        'prefix'    => 'signup',
    ], function() {

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
});