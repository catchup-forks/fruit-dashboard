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
});
