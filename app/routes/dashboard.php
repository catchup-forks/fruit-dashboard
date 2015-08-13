<?php

/**
 * Routes for dashboard pages
 * @see DashboardController
 */
Route::group([
        'prefix'    => 'dashboard',
    ], function() {

    Route::any('', [
        'before' => 'auth',
        'as'     => 'dashboard.dashboard',
        'uses'   => 'DashboardController@anyDashboard'
    ]);

    Route::get('manage', [
        'before' => 'auth',
        'as'     => 'dashboard.manage',
        'uses'   => 'DashboardController@getManageDashboards'
    ]);

    Route::any('delete/{dashboardId}', [
        'before' => 'auth',
        'as'     => 'dashboard.delete',
        'uses'   => 'DashboardController@anyDeleteDashboard'
    ]);

    Route::any('lock/{dashboardId}', [
        'before' => 'auth',
        'as'     => 'dashboard.lock',
        'uses'   => 'DashboardController@anyLockDashboard'
    ]);

    Route::any('unlock/{dashboardId}', [
        'before' => 'auth',
        'as'     => 'dashboard.lock',
        'uses'   => 'DashboardController@anyUnlockDashboard'
    ]);
});
