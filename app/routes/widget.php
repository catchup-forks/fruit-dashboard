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

    Route::any('reset/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.reset',
        'uses'   => 'GeneralWidgetController@anyResetWidget'
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

    Route::post('add/{descriptorID}', [
        'before' => 'auth',
        'as'     => 'widget.doAdd',
        'uses'   => 'GeneralWidgetController@postAddWidget'
    ]);

    Route::get('{widgetID}/stats', [
        'before' => 'auth',
        'as'    => 'widget.singlestat',
        'uses'  => 'GeneralWidgetController@getSinglestat',
    ]);

    Route::any('{widgetID}/pin-to-dashboard/{resolution}', [
        'before' => 'auth',
        'as'    => 'widget.pin-to-dashboard',
        'uses'  => 'GeneralWidgetController@anyPinToDashboard',
    ]);

    Route::post('save-position', [
        'before' => 'auth',
        'as'    => 'widget.save-position',
        'uses'  => 'GeneralWidgetController@saveWidgetPosition',
    ]);

    Route::post('get/descriptor/', [
        'before' => 'auth',
        'as'    => 'widget.get-descriptor',
        'uses'  => 'GeneralWidgetController@getWidgetDescriptor',
    ]);

    Route::post('ajax-handler/{widgetID}', [
        'before' => 'auth',
        'as'    => 'widget.ajax-handler',
        'uses'  => 'GeneralWidgetController@ajaxHandler',
    ]);

});