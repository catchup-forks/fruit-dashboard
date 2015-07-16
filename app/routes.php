<?php

/**
 * --------------------------------------------------------------------------
 * Global app routes
 * --------------------------------------------------------------------------
 */
Route::get('/', function() {
    return Redirect::route('dashboard.dashboard');
});

/**
 * --------------------------------------------------------------------------
 * /signup | Signup wizard urls
 * --------------------------------------------------------------------------
 */
Route::controller('signup', 'SignupWizardController', array(
    'getAuthentication'     => 'signup.authentication',
    'postAuthentication'    => 'signup.authentication',
    'getPersonalWidgets'    => 'signup.personal-widgets',
    'postPersonalWidgets'   => 'signup.personal-widgets',
    'getFinancialConnect'   => 'signup.financial-connect',
    'postFinancialConnect'  => 'signup.financial-connect',
));

Route::get('signup', array(
    'as' => 'auth.signup',
    'uses' => 'AuthController@showSignup'
));

Route::post('signup', array(
    'as' => 'auth.signup',
    'uses' => 'AuthController@doSignup'
));


// sign in routes
Route::get('signin', array(
    'as' => 'auth.signin',
    'uses' => 'AuthController@showSignin'
));

Route::post('signin', array(
    'as' => 'auth.signin',
    'uses' => 'AuthController@doSignin'
));


// sign out route
Route::any('signout', array(
    'as' => 'auth.signout',
    'uses' => 'AuthController@doSignout'
));











/*
|--------------------------------------------------------------------------
| Dev routes (these routes are for testing API-s only)
|--------------------------------------------------------------------------
*/

if(!App::environment('production'))
{
    // braintree development routes

    Route::get('/braintree', array(
        'as' => 'dev.braintree',
        'uses' => 'DevController@showBraintree'
    ));

    Route::post('/braintree', array(
        'as' => 'dev.braintree',
        'uses' => 'DevController@doBraintreePayment'
    ));


    Route::get('/users', array(
        'before' => 'auth|api_key',
        'as' => 'dev.users',
        'uses' => 'DevController@showUsers'
    ));

    Route::get('/test', array(
        'as'   => 'dev.test',
        'uses' => 'DevController@showTest'
    ));

    Route::get('/email/{email}', array(
        'as'    => 'dev.email',
        'uses'  => 'DevController@showEmail'
    ));
}




// metric graph routes
Route::get('dashboard', array(
    'before' => 'trial_ended',
    'as' => 'dashboard.dashboard',
    'uses' => 'DashboardController@showDashboard'
));

Route::get('statistics/{statID}', array(
    'before' => 'auth|trial_ended|cancelled|api_key',
    'as' => 'dashboard.single_stat',
    'uses' => 'DashboardController@showSinglestat'
));


// settings routes
Route::get('settings', array(
    'before' => 'auth',
    'as' => 'settings.settings',
    'uses' => 'SettingsController@showSettings'
));

Route::post('settingsName', array(
    'before' => 'auth',
    'uses' => 'SettingsController@doSettingsName'
));

Route::post('settingsCountry', array(
    'before' => 'auth',
    'uses' => 'SettingsController@doSettingsCountry'
));

Route::post('settingsEmail', array(
    'before' => 'auth',
    'uses' => 'SettingsController@doSettingsEmail'
));

Route::post('settingsPassword', array(
    'before' => 'auth',
    'uses' => 'SettingsController@doSettingsPassword'
));

Route::post('settingsFrequency', array(
    'before' => 'auth',
    'uses' => 'SettingsController@doSettingsFrequency'
));

Route::post('cancelSubscription', array(
    'before'    => 'auth',
    'uses'      => 'PaymentController@doCancelSubscription'
));

Route::post('doSignupOnDashboard', array(
    'uses'      => 'AuthController@doSignup'
));


// connect routes
Route::get('connect', array(
    // 'before' => 'auth|trial_ended|cancelled',
    'as' => 'connect.connect',
    'uses' => 'ConnectController@showConnect'
));

Route::post('connectBraintree',array(
    'before'    => 'auth',
    'uses'      => 'ConnectController@doBraintreeConnect'
));

Route::any('import/{provider}',array(
    'before'    => 'auth',
    'uses'      => 'ConnectController@doImport'
));

Route::any('connect/{provider}/{step?}', array(
    'before' => 'auth',
    'uses' => 'ConnectController@connectWizard'
));

Route::any('connect/new/{provider?}/{step?}', array(
    'before' => 'auth',
    'as'    => 'connect/new',
    'uses' => 'ConnectController@connectWizard'
));

Route::post('connect', array(
    'before' => 'auth',
    'as' => 'connect.connect',
    'uses' => 'ConnectController@doConnect'
));

Route::post('suggest', array(
    'before' => 'auth',
    'as'    => 'auth.suggest',
    'uses' => 'ConnectController@doSaveSuggestion'
));

// disconnect
Route::get('/disconnect/{service}', array(
    'before' => 'auth|api_key',
    'as' => 'auth.disconnect',
    'uses' => 'ConnectController@doDisconnect'
));

// delete widget
Route::any('connect.deletewidget/{widget_id}', array(
    'before' => 'auth',
    'as' => 'connect.deletewidget',
    'uses' => 'ConnectController@deleteWidget'
));

// edit widget
Route::get('connect.editwidget/{service}/{widget_id?}', array(
    'before' => 'auth',
    'as' => 'connect.editwidget',
    'uses' => 'ConnectController@editWidget'
));

// background settings, should be moved to separate package
Route::post('settingsBackground', array(
    'before' => 'auth',
    'uses' => 'ConnectController@doSettingsBackground'
));


// subscription routes
Route::get('/plans', array(
    'before'    => 'auth',
    'as'        => 'payment.plan',
    'uses'      => 'PaymentController@showPlans'
));

Route::get('/plans/trial', array(
    'before'    => 'auth',
    'as'        => 'payment.plan.trial',
    'uses'      => 'PaymentController@doTrial'
));

Route::post('/plans/{planName}', array(
    'before'    => 'auth',
    'as'        => 'payment.plan.name',
    'uses'      => 'PaymentController@doPayPlan'
));


// webhook endpoints
Route::get('/api/events/braintree/{webhookId}', array(
    'uses'      => 'WebhookController@verifyBraintreeWebhook',
));

Route::post('/api/events/braintree/{webhookId}', array(
    'uses'      => 'WebhookController@braintreeEvents',
));


// AJAX endpoints
Route::post('/widgets/save-position/{userId}/{position}', array(
    'uses'  => 'WidgetRESTController@saveWidgetPosition',
));

Route::post('/widgets/save-text/{widgetId}/{text?}', array(
    'uses'  => 'WidgetRESTController@saveWidgetText',
));

Route::post('/widgets/settings/name/{widgetId}/{newName}', array(
    'uses'  => 'WidgetRESTController@saveWidgetName',
));

Route::post('/widgets/settings/username/{newName}', array(
    'before'    => 'auth',
    'uses'  => 'WidgetRESTController@saveUserName',
));

/*
|--------------------------------------------------------------------------
| demo Routes
|--------------------------------------------------------------------------
*/

Route::get('demo', array(
    'as' => 'demo.dashboard',
    'uses' => 'DemoController@showDashboard'
));

Route::get('demo/dashboard', array(
    'as' => 'demo.dashboard',
    'uses' => 'DemoController@showDashboard'
));

Route::get('demo/statistics/{statID}', array(
    'as' => 'demo.single_stat',
    'uses' => 'DemoController@showSinglestat'
));



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/api/{apiVersion?}/{apiKey?}', array(
    'uses'  => 'ApiController@saveApiData',
));
