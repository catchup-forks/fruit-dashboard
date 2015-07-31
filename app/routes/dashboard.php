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
});
