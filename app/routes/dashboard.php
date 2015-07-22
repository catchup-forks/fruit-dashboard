<?php

/**
 * Routes for dashboard pages
 * @see DashboardController
 */
Route::group([
        'prefix'    => 'dashboard',
    ], function() {

    Route::any('', [
        'as'     => 'dashboard.dashboard',
        'uses'   => 'DashboardController@anyDashboard'
    ]);
});
