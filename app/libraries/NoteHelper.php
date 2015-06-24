<?php

class NoteHelper {

	public static function wizard($step = NULL){

		// save the widget
		$widgetData = array(
		);

		$widgetJson = json_encode($widgetData);
		$widget = new Widget;
		$widget->widget_name = 'note widget';
		$widget->widget_type = 'note';
		$widget->widget_source = $widgetJson;
		$widget->dashboard_id = Auth::user()->dashboards()->first()->id;
		$widget->position = '{"size_x":3,"size_y":3,"col":1,"row":1}';
		$widget->save();

		// save an empty data line
		$text = new Data;
		$text->widget_id = $widget->id;
		$text->data_object = '';
		$text->date = Carbon::now()->toDateString();
		$text->save();

		return Redirect::route('dashboard.dashboard')
			->with('success', 'Note widget added.');
	} # / function wizard


	public static function createDashboardData($widget){

		$dataArray = array();

		$widgetObject = json_decode($widget->widget_source);
		$current_value = Data::where('widget_id', $widget->id)->first()->data_object;
		$current_value = str_replace('[%LINEBREAK%]', "\n", $current_value);

		return [$current_value, $dataArray];

	} # / function createDashboardData


}