<?php

class IframeHelper {

	public static function wizard($step = NULL){

		if (!$step){
			return View::make('connect.connect-iframe')->with(array(
				'isBackgroundOn' => Auth::user()->isBackgroundOn,
				'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
			));
		}

		if ($step == 2) {
			
			$url = Input::get('fullURL');

			# save the widget
			$widget_data = array(
				'iframeURL'   => $url
			);
			$widget_json = json_encode($widget_data);

			$widget = new Widget;
			$widget->widget_name = 'iframe widget';
			$widget->widget_type = 'iframe';
			$widget->widget_source = $widget_json;
			$widget->dashboard_id = Auth::user()->dashboards()->first()->id;
			$widget->position = '{"size_x":6,"size_y":8,"col":1,"row":1}';
			$widget->save();

			return Redirect::route('dashboard.dashboard')
			  ->with('success', 'iframe widget added.');
		}
	} # / function wizard



	public static function createDashboardData($widget){

		$current_value = $widget->widget_source;
		$dataArray = array();

		return [$current_value, $dataArray];

	} # / function createDashboardData

}
