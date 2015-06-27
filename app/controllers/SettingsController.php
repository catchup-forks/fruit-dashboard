<?php


/*
|--------------------------------------------------------------------------
| SettingsController: Handles the settings related tasks
|--------------------------------------------------------------------------
*/
class SettingsController extends BaseController
{
	/*
	|===================================================
	| <GET> | showSettings: renders the settings page
	|===================================================
	*/
	public function showSettings()
	{
		// checking connections for the logged in user
		$user = Auth::user();
		
		// get users plan name
		$plans = Braintree_Plan::all();

		$planName = null;
		foreach ($plans as $plan) {
			if ($plan->id == $user->plan) {
				$planName = $plan->name;
			}
		}

		// no we found no plan, lets set one
		if (!$planName)
		{
			if($user->plan == 'free')
			{
				$planName = 'Free pack';
			}
			if($user->plan == 'trial')
			{
			   $planName = 'Trial period';
			}
			if($user->plan == 'cancelled')
			{
				$planName = 'Not subscribed';
			}
			if($user->plan == 'trial_ended')
			{
				$planName = 'Trial period ended';
			}
		}


		$client = GooglespreadsheetHelper::setGoogleClient();

		$google_spreadsheet_widgets = $user->dashboards()->first()->widgets()->where('widget_type', 'like', 'google-spreadsheet%')->get();
		$iframe_widgets = $user->dashboards()->first()->widgets()->where('widget_type', 'like', 'iframe%')->get();
		$quote_widgets = $user->dashboards()->first()->widgets()->where('widget_type', 'like', 'quote%')->get();
		$note_widgets = $user->dashboards()->first()->widgets()->where('widget_type','like','note%')->get();
		$clock_widgets = $user->dashboards()->first()->widgets()->where('widget_type','like','clock%')->get();
		$greeting_widgets = $user->dashboards()->first()->widgets()->where('widget_type','like','greeting%')->get();

		return View::make('settings.settings',
			array(
				'user'              => $user,
				
				// stripe stuff
				'stripeButtonUrl'   => OAuth2::getAuthorizeURL(),
				
				// google spreadsheet stuff 
				'googleSpreadsheetButtonUrl'    => $client->createAuthUrl(),
				
				// widgets
				'google_spreadsheet_widgets'    => $google_spreadsheet_widgets,
				'iframe_widgets'                => $iframe_widgets,
				'quote_widgets'                 => $quote_widgets,
				'note_widgets'                  => $note_widgets,
				'clock_widgets'									=> $clock_widgets,
				'greeting_widgets'              => $greeting_widgets,
				
				// payment stuff
				'planName'          => $planName,

				// background stuff
				'isBackgroundOn' => Auth::user()->isBackgroundOn,
				'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),

			)
		);
	}

	/*
	|===================================================
	| <POST> | doSettings: updates user data
	|===================================================
	*/
	public function doSettingsName()
	{
		// Validation rules
		$rules = array(
			'name' => 'required',
			);
		// run the validation rules on the inputs
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			// validation error -> redirect
			$failedAttribute = $validator->invalid();
			return Redirect::to('/settings')
				->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
				->withInput(); // sending back data
		} else {
			// validator success -> edit_profile
			// selecting logged in user
			$user = Auth::user(); 
			
			$user->name = Input::get('name');
				
			$user->save();
			// setting data
			return Redirect::to('/settings')
				->with('success', 'Nice to have you here, '.$user->name.'.');
		}
	}

	public function doSettingsCountry()
	{
		// Validation rules
		$rules = array(
			'country' => 'required',
			);

		// run the validation rules on the inputs
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			// validation error -> redirect
			$failedAttribute = $validator->invalid();
			return Redirect::to('/settings')
				->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
				->withInput(); // sending back data
		} else {

			// selecting logged in user
			$user = Auth::user();
			// if we have zoneinfo
			// changing zoneinfo
			$user->zoneinfo = Input::get('country');
			// saving user
			$user->save();

			// redirect to settings
			return Redirect::to('/settings')
				->with('success', 'Edit was successful.');
		}
	}

	public function doSettingsEmail()
	{
		// Validation rules
		$rules = array(
			'email' => 'required|unique:users,email|email',
			'email_password' => 'required|min:4',
			);
		// run the validation rules on the inputs
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			// validation error -> redirect
			$failedAttribute = $validator->invalid();
			return Redirect::to('/settings')
				->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
				->withInput(); // sending back data
		} else {
			// validator success -> edit_profile
			// selecting logged in user
			$user = Auth::user();
			
			// we need to check the password
			if (Hash::check(Input::get('email_password'), $user->password)){
				$user->email = Input::get('email');
			}
				
			$user->save();
			// setting data
			return Redirect::to('/settings')
				->with('success', 'Edit was successful.');
		}
	}

	public function doSettingsPassword()
	{
		// Validation rules
		$rules = array(
			'old_password' => 'required|min:4',
			'new_password' => 'required|confirmed|min:4',
		);
		// run the validation rules on the inputs
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			// validation error -> redirect
			$failedAttribute = $validator->invalid();
			return Redirect::to('/settings')
				->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
				->withInput(); // sending back data
		} else {
			// validator success -> edit_profile
			// selecting logged in user
			$user = Auth::user();
			
			// if we have data from the password change form
			// checking if old password is the old password
			if (Hash::check(Input::get('old_password'), $user->password)){
				$user->password = Hash::make(Input::get('new_password'));
			}
			else {
				return Redirect::to('/settings')
					->with('error', 'The old password you entered is incorrect.'); // send back errors
			}  
				
			$user->save();
			// setting data
			return Redirect::to('/settings')
				->with('success', 'Edit was successful.');
		}
	}

	public function doSettingsFrequency()
	{
		$user = Auth::user();

		$user->summaryEmailFrequency = Input::get('new_frequency');

		$user->save();

		return Redirect::to('/settings')
			->with('success', 'Edit was succesful.');
	}

}

