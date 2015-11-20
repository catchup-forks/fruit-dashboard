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

    Route::get('{WidgetId}/setting/ajax/{fieldName}/{value}', [
        'before' => 'auth',
        'as'    => 'widget.get-ajax-setting',
        'uses'  => 'GeneralWidgetController@getAjaxSetting',
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

    Route::get('add/{descriptorId}/{dashboardId}', [
        'before' => 'auth',
        'as'     => 'widget.add-with-data',
        'uses'   => 'GeneralWidgetController@getAddWidgetWithData'
    ]);

    Route::get('/stats/{widgetID}', [
        'before' => 'auth',
        'as'    => 'widget.singlestat',
        'uses'  => 'GeneralWidgetController@getSinglestat',
    ]);

    Route::any('/pin-to-dashboard/{widgetID}{resolution}', [
        'before' => 'auth',
        'as'    => 'widget.pin-to-dashboard',
        'uses'  => 'GeneralWidgetController@anyPinToDashboard',
    ]);

    Route::post('save-position', [
        'before' => 'auth',
        'as'    => 'widget.save-position',
        'uses'  => 'GeneralWidgetController@saveWidgetPosition',
    ]);

    Route::any('/save-layout/{widgetId}/{layout}', [
        'before' => 'auth',
        'as'    => 'widget.save-layout',
        'uses'  => 'GeneralWidgetController@saveLayout',
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

    Route::post('share/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.share',
        'uses'   => 'GeneralWidgetController@postShareWidget'
    ]);

    Route::any('share/accept/{sharingId}', [
        'before' => 'auth',
        'as'     => 'widget.share.accept',
        'uses'   => 'GeneralWidgetController@anyAcceptShare'
    ]);

    Route::any('share/reject/{sharingId}', [
        'before' => 'auth',
        'as'     => 'widget.share.reject',
        'uses'   => 'GeneralWidgetController@anyRejectShare'
    ]);

    Route::any('to-image/{widgetID}', [
        'before' => 'auth',
        'as'     => 'widget.to-image',
        'uses'   => 'GeneralWidgetController@anySaveWidgetToImage'
    ]);

    Route::any('sharing/accept/all', [
        'before' => 'auth',
        'as'     => 'widget.accept.all',
        'uses'   => 'GeneralWidgetController@acceptWidgetSharings'
    ]);

});
