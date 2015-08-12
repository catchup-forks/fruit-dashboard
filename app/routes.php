<?php

/**
 * --------------------------------------------------------------------------
 * Root url
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
include 'routes/signup-wizard.php';

/**
 * --------------------------------------------------------------------------
 * /auth | Authentication urls
 * --------------------------------------------------------------------------
 */
include 'routes/auth.php';

/**
 * --------------------------------------------------------------------------
 * /settings | Settings urls
 * --------------------------------------------------------------------------
 */
include 'routes/settings.php';

/**
 * --------------------------------------------------------------------------
 * /dashboard | Dashboard urls
 * --------------------------------------------------------------------------
 */
include 'routes/dashboard.php';

/**
 * --------------------------------------------------------------------------
 * /widget | Widget urls
 * --------------------------------------------------------------------------
 */
include 'routes/widget.php';

/**
 * --------------------------------------------------------------------------
 * /payment | Payment and subscription related sites
 * --------------------------------------------------------------------------
 */
include 'routes/payment.php';

/**
 * --------------------------------------------------------------------------
 * /service | Service connection related sites.
 * --------------------------------------------------------------------------
 */
include 'routes/service-connection.php';

/**
 * --------------------------------------------------------------------------
 * /dev | Testing urls (except for production server)
 * --------------------------------------------------------------------------
 */
include 'routes/development.php';

/**
 * --------------------------------------------------------------------------
 * /dashboard | Dashboard management sites
 * --------------------------------------------------------------------------
 */
/**
 * @todo: This route originally used the 'before' => 'trial_ended' filter.
 */
/**
 * @todo: This route should be removed from here
 */
Route::get('statistics/{statID}', array(
    'before' => 'auth|trial_ended|cancelled|api_key',
    'as' => 'dashboard.single_stat',
    'uses' => 'DashboardController@showSinglestat'
));


/**
 * @todo: Development ROUTES should be moved into separated controllers
 */
/*
|--------------------------------------------------------------------------
| Dev routes (these routes are for testing API-s only)
|--------------------------------------------------------------------------
*/

if(!App::environment('production'))
{
    // braintree development routes

    Route::get('/braintree', array(
        'as' => 'development.braintree',
        'uses' => 'DevController@showBraintree'
    ));

    Route::post('/braintree', array(
        'as' => 'development.braintree',
        'uses' => 'DevController@doBraintreePayment'
    ));

    Route::get('/users', array(
        'before' => 'auth|api_key',
        'as' => 'development.users',
        'uses' => 'DevController@showUsers'
    ));

    Route::get('/test', array(
        'as'   => 'development.test',
        'uses' => 'DevController@showTest'
    ));

    Route::get('/email/{email}', array(
        'as'    => 'development.email',
        'uses'  => 'DevController@showEmail'
    ));
}

/**
 * @todo: Webhook endpoints are now obsolete --> Remove from code
 */
// webhook endpoints
Route::get('/api/events/braintree/{webhookId}', array(
    'uses'      => 'WebhookController@verifyBraintreeWebhook',
));

Route::post('/api/events/braintree/{webhookId}', array(
    'uses'      => 'WebhookController@braintreeEvents',
));

// AJAX endpoints
Route::post('/widgets/save-text/{widgetId}/{text?}', array(
    'uses'  => 'GeneralWidgetController@saveWidgetText',
));

Route::post('/widgets/settings/name/{widgetId}/{newName}', array(
    'uses'  => 'GeneralWidgetController@saveWidgetName',
));

Route::post('/widgets/settings/username/{newName}', array(
    'before'    => 'auth',
    'uses'  => 'GeneralWidgetController@saveUserName',
));

/**
 * @todo: No demo sites are available in the current version
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


/**
 * @todo: Remove this if nobody uses
 */
Route::post('/api/{apiVersion?}/{apiKey?}', array(
    'uses'  => 'ApiController@saveApiData',
));
