<?php

class GreetingHelper {

	public static function wizard($step = NULL){

		// save the widget
		$widgetData = array();
		$widgetJson = json_encode($widgetData);

		$widget = new Widget;
		$widget->widget_name = 'greetings widget';
		$widget->widget_type = 'greeting';
		$widget->widget_source = $widgetJson;
		$widget->dashboard_id = Auth::user()->dashboards()->first()->id;
		$widget->position = '{"size_x":4,"size_y":3,"col":1,"row":1}';
		$widget->save();

		return Redirect::route('dashboard.dashboard')
		  ->with('success', 'Greetings widget added.');
	} # / function wizard


	public static function createDashboardData($widget){

		$dataArray = array();

		$widgetObject = json_decode($widget->widget_source);
		$current_value = '';

		return [$current_value, $dataArray];

	} # / function createDashboardData




}