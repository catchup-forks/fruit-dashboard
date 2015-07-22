<?php

/**
 * Routes for signup wizard pages
 * @see SignupWizardController
 */
Route::group([
        'prefix'    => 'signup',
    ], function() {

    Route::get('authentication', [
        'as'     => 'signup.authentication',
        'uses'   => 'SignupWizardController@getAuthentication'
    ]);

    Route::post('authentication', [
        'as'     => 'signup.authentication',
        'uses'   => 'SignupWizardController@postAuthentication'
    ]);

    Route::get('personal-widgets', [
        'as'     => 'signup.personal-widgets',
        'uses'   => 'SignupWizardController@getPersonalWidgets'
    ]);

    Route::post('personal-widgets', [
        'as'     => 'signup.personal-widgets',
        'uses'   => 'SignupWizardController@postPersonalWidgets'
    ]);

    Route::get('financial-connections', [
        'as'     => 'signup.financial-connections',
        'uses'   => 'SignupWizardController@getFinancialConnections'
    ]);

    Route::post('financial-connections', [
        'as'     => 'signup.financial-connections',
        'uses'   => 'SignupWizardController@postFinancialConnections'
    ]);
});