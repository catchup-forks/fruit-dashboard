<?php

/**
 * Routes for metric provider pages
 * @see MetricsController
 */
Route::group([
        'prefix' => 'metrics',
    ], function() {

    Route::get('users/registered', [
        'as'     => 'metrics.registeredUsers',
        'uses'   => 'MetricsController@getRegisteredUserCount'
    ]);

    Route::get('users/active', [
        'as'     => 'metrics.activeUsers',
        'uses'   => 'MetricsController@getActiveUserCount'
    ]);

    Route::get('users/has-widget/{service}', [
        'as'     => 'metrics.hasWidget',
        'uses'   => 'MetricsController@getServiceWidgetUsersCount'
    ]);

    Route::get('dashboards', [
        'as'     => 'metrics.numberOfDashboards',
        'uses'   => 'MetricsController@getNumberOfDashboards'
    ]);

    Route::get('widgets', [
        'as'     => 'metrics.numberOfWidgets',
        'uses'   => 'MetricsController@getNumberOfWidgets'
    ]);

    Route::get('datapoints', [
        'as'     => 'metrics.numberOfDataPoints',
        'uses'   => 'MetricsController@getNumberOfDataPoints'
    ]);
});
