<?php

class QuoteHelper {

	public static function wizard($step = NULL){

		if (!$step){
			return View::make('connect.connect-quote')
				->with(array(
					'isBackgroundOn' => Auth::user()->isBackgroundOn,
					'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
				)
			);
		}

		if ($step == 2) {
			
			$type = Input::get('type');
			$refresh = Input::get('refresh');
			$language = Input::get('language');                

			# save the widget
			$widget_data = array(
				'type'      =>  $type,
				'refresh'   =>  $refresh,
				'language'   =>  $language
			);
			$widget_json = json_encode($widget_data);

			$widget = new Widget;
			$widget->widget_name = 'quote widget';
			$widget->widget_type = 'quote';
			$widget->widget_source = $widget_json;
			$widget->dashboard_id = Auth::user()->dashboards()->first()->id;
			$widget->position = '{"size_x":6,"size_y":2,"col":1,"row":1}';
			$widget->save();

			return Redirect::route('dashboard.dashboard')
			  ->with('success', 'Quote widget added.');
		}
	} # / function wizard

	public static function createDashboardData($widget){

		$current_value = 0;
		$dataArray = array();

		$widgetObject = json_decode($widget->widget_source);

		if (!isset($widgetObject->language)) {
			$widgetObject->language = 'english';
		}
		if (!isset($widgetObject->refresh)) {
			$widgetObject->refresh = 'daily';
		}
		if (!isset($widgetObject->type)) {
			$widgetObject->type = 'quote-inspirational';
		}

		if ($widgetObject->refresh == 'every-refresh') {

			# if it needs to be refreshed at every query
			$quoteObject = Quote::where('type', '=', $widgetObject->type)
			->where('language', '=', $widgetObject->language)
			->orderBy(DB::raw('RAND()'))
			->first();

		} else {
			# if it needs to be refreshed daily

			# number of the day in the year
			$numberOfDayInYear = date('z');

			# get all the matching quotes
			$quotes = Quote::where('type', '=', $widgetObject->type)
			->where('language', '=', $widgetObject->language)
			->get();

			# count the quotes
			$quoteCount = $quotes->count();
			if ($quoteCount == 0) {
				$current_value = json_encode([
						'quote' => 'No quote for Johnny today.',
						'author' => 'Anonymous'
				]);
			} else {
		        # calculate which quote will we use
		        $quoteNumber = $numberOfDayInYear % $quoteCount;

		        # get the nth quote
		        $quoteObject = $quotes->get($quoteNumber);

				$current_value = json_encode([
						'quote' => $quoteObject->quote,
						'author' => $quoteObject->author
				]);

			}

		}

		return [$current_value, $dataArray];

	} # / function createDashboardData

}