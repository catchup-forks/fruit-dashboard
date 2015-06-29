<?php

class OnboardingHelper {

	public static function getConnectPageWidgetData(){
		$widgetData = [
			'provider' => 'onboarding',
			'caption' => 'Onboarding wizard',
			'icon' => 'fa-sign-in',
			'premium' => false,
		];
		return $widgetData;
	} # / function getConnectPageWidgetData


	public static function wizard($step = NULL){

		switch ($step) {

			case 'init':

				# render wizard step #1
				return View::make('connect.connect-onboarding')->with(array(
					'step' => 'show-greeting',
					'isBackgroundOn' => Auth::user()->isBackgroundOn,
					'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
				));
				break; # / case 'init'

			case 'save-user':

				Log::info(Input::get('tesztname'));

				# save user
				# FIXME

				# render wizard step #1
				return View::make('connect.connect-onboarding')->with(array(
					'step' => 'show-personal-widgets-wizard',
					'isBackgroundOn' => Auth::user()->isBackgroundOn,
					'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
				));
				break; # / case 'save-user'


			case 'save-personal-widgets':

				if (Input::has('tesztname2')) {
					// Log::info('Save button was pressed');
				} else {
					// Log::info('Skip button was pressed');
				}

				# save personal widgets
				# FIXME

				# render wizard step #1
				return Redirect::route('dashboard.dashboard')->with(array(
					'success' => 'Welcome to your dashboard.',
					'isBackgroundOn' => Auth::user()->isBackgroundOn,
					'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
				));
				break; # / case 'save-personal-widgets'


		} # / switch ($step)

	} # / function wizard


	public static function createDashboardData($widget){

		$current_value = 'just a moment.';
		$dataArray = array();

		return [$current_value, $dataArray];

	} # / function createDashboardData

}
