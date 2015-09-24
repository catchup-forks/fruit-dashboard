<?php

/**
 * Routes for Notification pages
 * @see NotificationController
 */
Route::group([
        'prefix'    => 'notification',
    ], function() {

    Route::any('test/', [
        'before' => 'auth',
        'as'     => 'notification.test',
        'uses'   => 'NotificationController@anyTest'
    ]);

    Route::any('send/{notificationId}', [
        'before' => 'auth',
        'as'     => 'notification.send',
        'uses'   => 'NotificationController@anySend'
    ]);
});
