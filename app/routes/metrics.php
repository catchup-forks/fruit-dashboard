<?php

/**
 * Routes for metric provider pages
 * @see MetricsController
 */
Route::group([
        'prefix'    => 'metrics',
    ], function() {

    Route::get('count/users/registered', [
        'as'     => 'metrics.registeredUsers',
        'uses'   => 'MetricsController@getRegisteredUserCount'
    ]);

    Route::get('count/users/active', [
        'as'     => 'metrics.activeUsers',
        'uses'   => 'MetricsController@getActiveUserCount'
    ]);

    Route::get('count/users/has-widget/{service}', [
        'as'     => 'metrics.hasWidget',
        'uses'   => 'MetricsController@getServiceWidgetUsersCount'
    ]);
});
