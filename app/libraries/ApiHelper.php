<?php

class ApiHelper {

	public static function wizard($step = NULL){

		// save the widget
		$widgetData = array();
		$widgetJson = json_encode($widgetData);

		$widget = new Widget;
		$widget->widget_name = 'API widget';
		$widget->widget_type = 'api';
		$widget->widget_source = $widgetJson;
		$widget->dashboard_id = Auth::user()->dashboards()->first()->id;
		$widget->position = '{"size_x":3,"size_y":3,"col":1,"row":1}';
		$widget->widget_ready = false;	# widget needs data to load to show properly
		$widget->save();

		$apiKey = base64_encode(json_encode(array(
			'wid'	=>	$widget->id
		)));
		$url = 'https://dashboard.tryfruit.com/api/0.1/'.$apiKey.'/';

		return View::make('connect.connect-api')
			->with(array(
				'url' => $url,
				'isBackgroundOn' => Auth::user()->isBackgroundOn,
				'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL()
		));

	} # / function wizard


	public static function createDashboardData($widget){

		$current_value = 'just a moment.';
		$dataArray = array();

		return [$current_value, $dataArray];

	} # / function createDashboardData

}
