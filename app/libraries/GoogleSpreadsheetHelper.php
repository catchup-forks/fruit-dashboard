<?php
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class GooglespreadsheetHelper {



	public static function wizard($step = NULL){

		# user is the authenticated user
		$user = Auth::user();

		# we will need a client for spreadsheet feeds + email + offline (to get a refreshtoken)
		$client = GooglespreadsheetHelper::setGoogleClient();

		switch ($step) {

			case 'init':

				# render wizard step #1
				return View::make('connect.connect-googlespreadsheet')->with(
					array(
						'step' => 'choose-type',
						'isBackgroundOn' => Auth::user()->isBackgroundOn,
						'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
					)
				);
				break; # /case 'init'

			case 'set-type':

				# save the widget type in SESSION
				if (Input::get('type')) {
					Session::put('type', Input::get('type'));
				}

				if (!$user->isGoogleSpreadsheetConnected()) {
					# if the user hasn't authorized with google
					# go to google oauth page
					$url = $client->createAuthUrl();
					return Redirect::to($url);
					break;
				} else {
					# otherwise render the spreadsheet chooser wizard page

					# get the spreadsheet list
					$access_token = GooglespreadsheetHelper::getGoogleAccessToken($client, $user);
					$serviceRequest = new DefaultServiceRequest($access_token);
					ServiceRequestFactory::setInstance($serviceRequest);
					$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
					$spreadsheetFeed = $spreadsheetService->getSpreadsheets();

					# render choose-spreadsheet wizard step
					return View::make('connect.connect-googlespreadsheet')->with(array(
						'step' => 'choose-spreadsheet',
						'spreadsheetFeed' => $spreadsheetFeed,
						'isBackgroundOn' => Auth::user()->isBackgroundOn,
						'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
						'type' => Session::get('type'),		
					));
				}
				break; # / case 'set-type'

			case 'set-spreadsheet':
				if (Input::get('type')) {
					Session::put('type', Input::get('type'));	
				}

				# save the spreadsheet ID in SESSION
				Session::put("spreadsheetId", Input::get('spreadsheetId'));

				# init service
				$access_token = GooglespreadsheetHelper::getGoogleAccessToken($client, $user);
				$serviceRequest = new DefaultServiceRequest($access_token);
				ServiceRequestFactory::setInstance($serviceRequest);

				# get the spreadsheet they asked for in the POST
				$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
				$spreadsheet = $spreadsheetService->getSpreadsheetById(Input::get('spreadsheetId'));

				# save the spreadsheet name in SESSION
				Session::put("spreadsheetName", $spreadsheet->getTitle());

				# get the worksheet list for the selected spreadsheet
				$worksheetFeed = $spreadsheet->getWorksheets();
				
				# render choose-worksheet wizard step
				return View::make('connect.connect-googlespreadsheet')->with(
					array(
						'step' => 'choose-worksheet',
						'worksheetFeed' => $worksheetFeed,
						'isBackgroundOn' => Auth::user()->isBackgroundOn,
						'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
					)
				);
				break; # /case 'set-spreadsheet'

			case 'set-worksheet':

				# save the worksheet name in SESSION
				Session::put("worksheetName", Input::get('worksheetName'));

				# save the widget
				$widget_data = array(
					'googleSpreadsheetId'   =>  Session::get('spreadsheetId'),
					'googleWorksheetName'   =>  Session::get('worksheetName')
				);
				$widget_json = json_encode($widget_data);

				$widget = new Widget;
				$widget->widget_name = Session::get('worksheetName').' - '.Session::get('spreadsheetName');
				$widget->widget_type = Session::get('type');
				$widget->widget_source = $widget_json;
				$widget->widget_ready = false;
				$widget->dashboard_id = $user->dashboards()->first()->id;
				$widget->position = '{"size_x":3,"size_y":4,"col":1,"row":1}';
				$widget->save();

				return Redirect::route('dashboard.dashboard')
				  ->with('success', 'Google Spreadsheet widget added.');
				break; # / case 'set-worksheet'

			default: // input has no step, this is the google auth return part

				# we got a code in GET from google
				if (Input::has('code')) {

					# lets get an access token
					try {
						$client->authenticate(Input::get('code'));
        			} catch (Exception $e) {
        				GooglespreadsheetHelper::disconnect();
						return Redirect::route('connect.connect')
						  ->with('error', 'Something went wrong, try again please.');
					}

					$credentials = $client->getAccessToken(); // big JSON stuff

					# lets make it an associative array
					$tokens_decoded = json_decode($credentials, true);

					# lets check if we have a refresh token already
					$refresh_token = $user->googleSpreadsheetRefreshToken;
					if (strlen($refresh_token)<10) {
						# nope, let's use the one we got now
						$refresh_token = $tokens_decoded['refresh_token'];
					}

					# database save the access-stuff-JSON and the refresh token
					$user->googleSpreadsheetCredentials = $credentials;
					$user->googleSpreadsheetRefreshToken = $refresh_token;
					$user->save();

					# good job, notify intercom
					IntercomHelper::connected($user,'googlespreadsheet');

					# auth
					$access_token = GooglespreadsheetHelper::getGoogleAccessToken($client, $user);

					# get the spreadsheet list
					$serviceRequest = new DefaultServiceRequest($access_token);
					ServiceRequestFactory::setInstance($serviceRequest);
					$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
					$spreadsheetFeed = $spreadsheetService->getSpreadsheets();

					# render choose-spreadsheet wizard step
					return View::make('connect.connect-googlespreadsheet')->with(array(
						'step' => 'choose-spreadsheet',
						'spreadsheetFeed' => $spreadsheetFeed,
						'isBackgroundOn' => Auth::user()->isBackgroundOn,
						'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),				
					));	
				} else {
					return View::make('connect.connect')->with(array(
						'error' => 'Something went wrong with the Google authentication.',
						'isBackgroundOn' => Auth::user()->isBackgroundOn,
						'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),				
					));
				}

				break; # / default

		} # /switch ($step)
	} # /function wizard




	public static function createDashboardData($widget){

		$current_value = "";
		$dataArray = array();

		switch ($widget->widget_type) {
			case 'google-spreadsheet-text-column':
				$dataObjects = Data::where('widget_id', $widget->id)
										->orderBy('date','asc')
										->get();
				foreach ($dataObjects as $dataObject) {
					$array = json_decode($dataObject->data_object, true);
					foreach ($array as $key => $value) {
						$current_value = $value;
						$dataArray = array_add($dataArray, $key, $current_value);
					}
				}
				break; # / case 'google-spreadsheet-text-column'

			case 'google-spreadsheet-text-column-random';
				$dataObject = Data::where('widget_id', $widget->id)
										->orderBy(DB::raw('RAND()'))
										->first();
				$array = json_decode($dataObject->data_object, true);
				$current_value = array_values($array)[0];
				break; # / case 'google-spreadsheet-text-column-random';

			default:
				$dataObjects = Data::where('widget_id', $widget->id)
										->orderBy('date','asc')
										->get();
				foreach ($dataObjects as $dataObject) {
					try {
						$array = json_decode($dataObject->data_object, true);
						if (!empty($array)) {
							$current_value = array_values($array)[0];
							$dataArray = array_add($dataArray, $dataObject->date, $current_value);							
						}
					} catch (Exception $e) {
						Log::error($e);
					}
				}
				break; # / default

		} # / switch ($widget->$widget_type) {

		return [$current_value, $dataArray];

	} # / function createDashboardData



	public static function disconnect($user){

		$refreshToken = $user->googleSpreadsheetRefreshToken;

		$user->googleSpreadsheetRefreshToken = "";
		$user->googleSpreadsheetCredentials = "";
		$user->googleSpreadsheetEmail = "";
		$user->save();

		$guzzle_client = new GuzzleHttp\Client();
		try {
			$response = $guzzle_client->get("https://accounts.google.com/o/oauth2/revoke?token=".$refreshToken);
        } catch (Exception $e) {
        	Log::error($e);
        }

		return true;

	} # /function disconnect

	public static function setGoogleClient(){
	    $client = new Google_Client();
	    $client->setClientId($_ENV['GOOGLE_CLIENTID']);
	    $client->setClientSecret($_ENV['GOOGLE_CLIENTSECRET']);
	    $client->setRedirectUri($_ENV['GOOGLE_REDIRECTURL']);
	    $client->setScopes(array('https://spreadsheets.google.com/feeds', 'email'));
	    $client->setAccessType('offline');                
	    $client->setApprovalPrompt('force');
	    return $client;
	} # /function setGoogleClient

	public static function getGoogleAccessToken($client, $user){

	    # load the tokens from the database
	    $credentials = $user->googleSpreadsheetCredentials;
	    $refresh_token = $user->googleSpreadsheetRefreshToken;

	    # give it a try
        try {
			$client->setAccessToken($credentials);
        } catch (Exception $e) {
        	Log::error($e);
        	exit();
        }

	    # if the token is expired, 
	    if ($client->isAccessTokenExpired()) {

	        # let's get another one with the refreshtoken
	        $refresh_token = $user->googleSpreadsheetRefreshToken;
	        try {        
	        	$client->refreshToken($refresh_token);
	        } catch (Exception $e) {
	        	# something went wrong, better disconnect the service
	        	Log::error($e);

				$refreshToken = $user->googleSpreadsheetRefreshToken;

				$user->googleSpreadsheetRefreshToken = "";
				$user->googleSpreadsheetCredentials = "";
				$user->googleSpreadsheetEmail = "";
				$user->save();

				$guzzle_client = new GuzzleHttp\Client();
				try {
					$response = $guzzle_client->get("https://accounts.google.com/o/oauth2/revoke?token=".$refreshToken);
		        } catch (Exception $e) {
		        	Log::error($e);
		        	exit();
		        }
	        }

	        # get new credentials
	        $credentials = $client->getAccessToken();

	        # decode 
	        $tokens_decoded = json_decode($credentials);
	        try {
	            $refresh_token = $tokens_decoded->refresh_token;
	        } catch (Exception $e) {}

	        # save them to the database
	        $user->googleSpreadsheetCredentials = $credentials;
	        $user->googleSpreadsheetRefreshToken = $refresh_token;
	    }

	    # get the real access_token (from the big JSON one)
	    $tokens_decoded = json_decode($credentials);
	    $access_token = $tokens_decoded->access_token;

	    return $access_token;
	} # /function getGoogleAccessToken

} # /class GooglespreadsheetHelper