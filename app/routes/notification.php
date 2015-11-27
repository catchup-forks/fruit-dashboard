<?php

/**
 * Routes for Notification pages
 * @see NotificationController
 */
Route::group([
        'prefix'    => 'notification',
    ], function() {

    Route::get('slack/configure', [
        'before' => 'auth',
        'as'     => 'notification.configureSlack',
        'uses'   => 'NotificationController@getConfigureSlack'
    ]);

    Route::post('slack/configure', [
        'before' => 'auth',
        'as'     => 'notification.configureSlack',
        'uses'   => 'NotificationController@postConfigureSlack'
    ]);

    Route::any('slack/send', [
        'before' => 'auth',
        'as'     => 'notification.sendSlackMessage',
        'uses'   => 'NotificationController@anySendSlackMessage'
    ]);

    Route::any('test/', [
        'before' => 'auth',
        'as'     => 'notification.test',
        'uses'   => 'NotificationController@anyTest'
    ]);

    Route::any('send/{id}', [
        'before' => 'auth',
        'as'     => 'notification.send',
        'uses'   => 'NotificationController@anySend'
    ]);

    Route::post('widgets/{notificationId}', [
        'before' => 'auth',
        'as'     => 'notification.widgets',
        'uses'   => 'NotificationController@postWidgets'
    ]);
});
