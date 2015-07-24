<?php

/**
 * Routes for widget pages
 * @see GeneralWidgetController
 */
Route::group([
        'prefix' => 'widget',
    ], function() {

    Route::get('{widgetID}/edit-settings', [
        'before' => 'auth',
        'as'     => 'widget.edit-settings',
        'uses'   => 'GeneralWidgetController@getEditWidgetSettings'
    ]);

    Route::post('{widgetID}/edit-settings', [
        'before' => 'auth',
        'as'     => 'widget.edit-settings',
        'uses'   => 'GeneralWidgetController@postEditWidgetSettings'
    ]);

    Route::get('{widgetID}/setup', [
        'before' => 'auth',
        'as'     => 'widget.setup',
        'uses'   => 'GeneralWidgetController@getSetupWidget'
    ]);

    Route::post('{widgetID}/setup', [
        'before' => 'auth',
        'as'     => 'widget.setup',
        'uses'   => 'GeneralWidgetController@postSetupWidget'
    ]);

    Route::get('add', [
        'before' => 'auth',
        'as'     => 'widget.add',
        'uses'   => 'GeneralWidgetController@getAddWidget'
    ]);

    Route::get('add/{descriptorID}', [
        'before' => 'auth',
        'as'     => 'widget.doAdd',
        'uses'   => 'GeneralWidgetController@doAddWidget'
    ]);

   /**
     * ------------------------------------------------------------------------
     * AJAX endpoints | Widget settings
     * ------------------------------------------------------------------------
     */
    Route::post('/save-position/{userId}', array(
        'uses'  => 'GeneralWidgetController@saveWidgetPosition',
    ));
});