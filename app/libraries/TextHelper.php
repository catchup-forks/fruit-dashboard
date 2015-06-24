<?php

class TextHelper {

	public static function wizard($step = NULL){

		switch ($step) {

			case 'init':
				return View::make('connect.connect-text')->with(array(
					'step' => 'select-source',
					'isBackgroundOn' => Auth::user()->isBackgroundOn,
					'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),				
				));
				break;

			case 'source-selected':
				switch (Input::get('source')) {
					case 'text':
						return View::make('connect.connect-text')->with(array(
							'step' => 'enter-text',
							'isBackgroundOn' => Auth::user()->isBackgroundOn,
							'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),				
						));
						break;
					case 'google-spreadsheet-text-cell':
						return Redirect::to('connect/new/googlespreadsheet/set-type')->with(array(
							'type' => Input::get('source')
						));
						break;
					case 'google-spreadsheet-text-column-random':
						return Redirect::to('connect/new/googlespreadsheet/set-type')->with(array(
							'type' => Input::get('source')
						));
						break;
					default:
						return Redirect::route('connect.connect')
							->with('error', 'Something went wrong.');
						break;
				}		
			break; # /source-selected

			case 'save-text':

				$widget = new Widget;
				$widget->widget_name = 'text widget';
				$widget->widget_type = 'text';
				$widget->widget_source = Input::get('text');
				$widget->widget_ready = true;
				$widget->dashboard_id = Auth::user()->dashboards()->first()->id;
				$widget->position = '{"size_x":6,"size_y":2,"col":1,"row":1}';
				$widget->save();

				return Redirect::route('dashboard.dashboard')
				  ->with('success', 'Text widget added.');
				break;

			default:
				return Redirect::route('connect.connect')
					->with('error', 'Something went wrong.');
				break;

		} # / switch ($step)
	} # / function wizard



	public static function createDashboardData($widget){

		$current_value = $widget->widget_source;
		$dataArray = array();

		return [$current_value, $dataArray];

	} # / function createDashboardData



} # / class TextHelper