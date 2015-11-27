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
});
