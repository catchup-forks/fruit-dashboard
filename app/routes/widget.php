<?php

/**
 * Routes for widget pages
 * @see GeneralWidgetController
 */
Route::group([
        'prefix' => 'widget',
    ], function() {

    Route::get('edit/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.edit',
        'uses'   => 'GeneralWidgetController@getEditWidgetSettings'
    ]);

    Route::post('edit/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.edit',
        'uses'   => 'GeneralWidgetController@postEditWidgetSettings'
    ]);

    Route::get('setup/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.setup',
        'uses'   => 'GeneralWidgetController@getSetupWidget'
    ]);

    Route::post('setup/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.setup',
        'uses'   => 'GeneralWidgetController@postSetupWidget'
    ]);

    Route::any('delete/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.delete',
        'uses'   => 'GeneralWidgetController@anyDeleteWidget'
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

    Route::post('save-position/{userID}', array(
        'before' => 'auth',
        'as'    => 'widget.save-position',
        'uses'  => 'GeneralWidgetController@saveWidgetPosition',
    ));

    Route::post('ajax-handler/{widgetID}', array(
        'before' => 'auth',
        'as'    => 'widget.ajax-handler',
        'uses'  => 'GeneralWidgetController@ajaxHandler',
    ));
});