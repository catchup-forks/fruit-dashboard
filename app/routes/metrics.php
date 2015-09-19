<?php

/**
 * Routes for metric provider pages
 * @see MetricsController
 */
Route::group([
        'prefix' => 'metrics',
    ], function() {

    Route::get('users/{dimension}', [
        'as'     => 'metrics.getUserCount',
        'uses'   => 'MetricsController@getUserCount'
    ]);

    Route::get('vanity/{dimension}', [
        'as'     => 'metrics.getVanityCount',
        'uses'   => 'MetricsController@getVanityCount'
    ]);

    Route::get('connections/{service}', [
        'as'     => 'metrics.getConnectionsCount',
        'uses'   => 'MetricsController@getConnectionsCount'
    ]);

    Route::get('users/has-widget/{service}', [
        'as'     => 'metrics.hasWidget',
        'uses'   => 'MetricsController@getServiceWidgetUsersCount'
    ]);

    Route::get('services/widgets/', [
        'as'     => 'metrics.',
        'uses'   => 'MetricsController@getAllServiceWidgetsCount'
    ]);

});
