<?php
/**
 * Routes for widget pages
 * @see GeneralWidgetController
 */
Route::group([
        'prefix' => 'widget',
    ], function() {

    Route::get('{widgetID}/edit-settings', [
        'as'     => 'widget.edit-settings',
        'uses'   => 'GeneralWidgetController@getEditWidgetSettings'
    ]);

    Route::post('{widgetID}/edit-settings', [
        'as'     => 'widget.edit-settings',
        'uses'   => 'GeneralWidgetController@postEditWidgetSettings'
    ]);

    Route::get('{widgetID}/setup', [
        'as'     => 'widget.setup',
        'uses'   => 'GeneralWidgetController@getEditWidgetSettings'
    ]);

    Route::post('{widgetID}/setup', [
        'as'     => 'widget.setup',
        'uses'   => 'GeneralWidgetController@postEditWidgetSettings'
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