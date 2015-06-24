<?php


/*
|--------------------------------------------------------------------------
| AuthController: Handles the authentication related sites
|--------------------------------------------------------------------------
*/
class DashboardController extends BaseController
{
	/*
	|===================================================
	| <GET> | showDashboard: renders the dashboard page
	|===================================================
	*/
	public function showDashboard()
	{

		# if the user is not logged in, load user with id=1

		if (Auth::guest()) {
			Auth::loginUsingId(1);
		}
		$user = Auth::user();

		#####################################################
		# prepare stuff for stripe & braintree metrics start

		$allMetrics = array();

		if (Auth::user()->ready != 'notConnected') {
			$currentMetrics = Calculator::currentMetrics();

			$metricValues = Metric::where('user', Auth::user()->id)
									->orderBy('date','desc')
									->take(31)
									->get();

			foreach ($currentMetrics as $statID => $statDetails) {

				$metricsArray = array();
				foreach ($metricValues as $metric) {
					$metricsArray[$metric->date] = $metric->$statID;
				}
				ksort($metricsArray);
				
				$array = $statDetails['metricClass']::show($metricsArray);
				$array = array_add($array, 'widget_type', 'financial');
				$allMetrics[] = $array;
			}
		}

		# prepare stuff for stripe & braintree metrics end
		#####################################################

		#####################################################
		# prepare stuff for other widgets start

        if (Auth::user()->dashboards->count() == 0) {

			# this probably shouldn't happen

            // create first dashboard for user
            $dashboard = new Dashboard;
            $dashboard->dashboard_name = "Dashboard #1";
            $dashboard->save();

            // attach dashboard & user
            Auth::user()->dashboards()->attach($dashboard->id, array('role' => 'owner'));

            // create default widgets

            // clock widget
            $widget = new Widget;
            $widget->widget_name = 'clock widget';
            $widget->widget_type = 'clock';
            $widget->widget_source = '{}';
            $widget->position = '{"size_x":6,"size_y":4,"col":3,"row":1}';
            $widget->dashboard_id = $user->dashboards()->first()->id;
            $widget->save();

            // greeting widget
            $widget = new Widget;
            $widget->widget_name = 'greeting widget';
            $widget->widget_type = 'greeting';
            $widget->widget_source = '{}';
            $widget->position = '{"size_x":6,"size_y":3,"col":3,"row":5}';
            $widget->dashboard_id = $user->dashboards()->first()->id;
            $widget->save();

            // quote widget
            $widget = new Widget;
            $widget->widget_name = 'quote widget';
            $widget->widget_type = 'quote';
			$widget_data = array(
				'type'      =>  'quote-inspirational',
				'refresh'   =>  'daily',
				'language'   =>  'english'
			);
			$widget_json = json_encode($widget_data);
            $widget->widget_source = $widget_json;
            $widget->position = '{"size_x":10,"size_y":1,"col":2,"row":8}';
            $widget->dashboard_id = $user->dashboards()->first()->id;
            $widget->save();
        }

		$widgets = Auth::user()->dashboards()->first()->widgets;

		foreach ($widgets as $widget) {

			# update widget_provider, if not set
			if (!$widget->widget_provider) {
				if (strpos('google-spreadsheet', $widget->widget_type)!==false) {
					$widget->widget_provider = 'googlespreadsheet';
				} else {
					$widget->widget_provider = $widget->widget_type;
				}
				$widget->save();
			}

			$current_value = "";
			$dataArray = array();

			if ($widget->widget_ready == true) {

				# create class name, f.e. stripe --> StripeHelper
				$widgetClassName = ucfirst($widget->widget_provider).'Helper';

				# method name
				$widgetMethodName = 'createDashboardData';

				# check if class & method exists, f.e. StripeHelper::createDashboardData
				if(class_exists($widgetClassName) && method_exists($widgetClassName, $widgetMethodName)){

					# it does, create dashboard data
					list($current_value, $dataArray) = $widgetClassName::$widgetMethodName($widget);

				} else {

					# it does not, log an error
					Log::error($widget->widget_type.' does not exist.');
				}
			}


			$widgetPosition = json_decode($widget->position);
			$position = [
				'x'     => $widgetPosition->size_x,
				'y'     => $widgetPosition->size_y,
				'col'   => $widgetPosition->col,
				'row'   => $widgetPosition->row,
			];

			$newMetricArray = array(
				"widget_id" => $widget->id,
				"widget_type" => $widget->widget_type,
				"widget_position" => $position,
				"widget_type" => $widget->widget_type,
				"widget_ready" => $widget->widget_ready,
				"statName" => str_limit($widget->widget_name, $limit = 25, $end = '...'),
				"fullName" => $widget->widget_name,
				"positiveIsGood" => "true",
				"history" => $dataArray,
				"currentValue" => $current_value,
				"oneMonthChange" => "",
				"position" => $position,
			);
			$allMetrics[] = $newMetricArray;
		} // /foreach
		
		# prepare stuff for other widgets end
		#####################################################

		$user = Auth::user();
		$client = GooglespreadsheetHelper::setGoogleClient();

		return View::make('dashboard.dashboard',
			array(
				'user'                          => $user,
				// widgets
				'allFunctions' 									=> $allMetrics,
				
				// stripe stuff
				'stripeButtonUrl'               => OAuth2::getAuthorizeURL(),

				// braintree stuff
				//'braintree_connect_stepNumber'  => $braintree_connect_stepNumber,

				// google spreadsheet stuff
				'googleSpreadsheetButtonUrl'    => $client->createAuthUrl(),

				// background stuff
				'isBackgroundOn' 								=> $user->isBackgroundOn,
				'dailyBackgroundURL' 						=> $user->dailyBackgroundURL(),
				
				// to hide home button if on dashboard
				'onDashboard' 									=> true,
			)
		);
	}

	/*
	|===================================================
	| <GET> | showSinglestat: renders the single stats page
	|===================================================
	*/
	public function showSinglestat($statID)
	{
/*
		// check if trial period is ended
		if (Auth::user()->isTrialEnded())
		{
			return Redirect::route('auth.plan')
				->with('error','Trial period ended.');
		}
*/
		
		#####################################################
		# prepare stuff for stripe & braintree metrics start

		$currentMetrics = Calculator::currentMetrics();

		# if the query goes for a stripe/braintree metric
		if (array_key_exists($statID, $currentMetrics)) {
			$metricValues = Metric::where('user', Auth::user()->id)
									->orderBy('date','desc')
									->take(31)
									->get();
			
			foreach ($currentMetrics as $metricID => $statClassName) {
				$metricsArray = array();
				foreach ($metricValues as $metric) {
					$metricsArray[$metric->date] = $metric->$metricID;
				}
				ksort($metricsArray);
				$allMetrics[$metricID] = $metricsArray;
			}

			if (isset($currentMetrics[$statID]))
			{
				$widgets = Auth::user()->dashboards->first()->widgets;

				return View::make('auth.single_stat',
					array(
						'data' => $currentMetrics[$statID]['metricClass']::show($allMetrics[$statID],true),
						'metricDetails' => $currentMetrics[$statID],
						'currentMetrics' => $currentMetrics,
						'widgets' => $widgets,
						'metric_type' => 'financial',
						'isFinancialStuffConnected' => Auth::user()->isFinancialStuffConnected()
					)
				);
			} else {
				return Redirect::route('dashboard.dashboard')
					->with('error', 'Widget does not exist.');
			}
		} else 

		# prepare stuff for stripe & braintree metrics end
		#####################################################

		#####################################################
		# prepare stuff for other metrics start

		{

			$widget = Widget::where("id", "=", $statID)->first();

			if (!$widget || $widget->data()->count() == 0) {
				return Redirect::route('dashboard.dashboard')
					->with('error', 'This widget is not yet filled with data. Try again in a few minutes.');                
			}

			# get min/max date
			$date_min = $widget->data()->min('date');
			$date_max = $widget->data()->max('date');

			# convert Y-m-d format to d-m-Y
			$date_min = DateTime::createFromFormat('Y-m-d', $date_min)->format('d-m-Y');
			$date_max = DateTime::createFromFormat('Y-m-d', $date_max)->format('d-m-Y');

			# make fullHistory

			# get the distinct dates
			$allData = $widget->data()->select('date')->groupBy('date')->get();


			# get 1 entry for each date
			$fullDataArray = array();
			$current_value = "";

			foreach($allData as $entry) {
				$dataObject = $widget->data()->where('date', '=', $entry->date)->first();
				$array = json_decode($dataObject->data_object, true);
				$current_value = intval(array_values($array)[0]);
				Log::info($current_value);
				$fullDataArray = array_add($fullDataArray, $dataObject->date, $current_value);
			}

			// $dataArray = array();
			$dataArray = $fullDataArray;

			$data = array(
					"id" => $widget->id,
					"statName" => $widget->widget_name,
					"positiveIsGood" => 1,
					"history" => $dataArray,
					"currentValue" => $current_value,
					"oneMonthChange" => "",
					"firstDay" => $date_min,
					"fullHistory" => $fullDataArray,
					"oneMonth" => "",
					"sixMonth" => "",
					"oneYear" => "",
					"twoMonthChange" => "",
					"threeMonthChange" => "",
					"sixMonthChange" => "",
					"nineMonthChange" => "",
					"oneYearChange" => "",
					"dateInterval" => Array(
						"startDate" => $date_min,
						"stopDate" => $date_max
					)
			);

			$metricDetails = array(
					"metricClass" => $widget->id,
					"metricName" => "",
					"metricDescription" => $widget->widget_name
			);

			$widgets = Auth::user()->dashboards->first()->widgets;

			return View::make('dashboard.single_stat',
				array(
					'data' => $data,
					'metricDetails' => $metricDetails,
					'currentMetrics' => $currentMetrics,
					'widgets' => $widgets,
					'metric_type' => 'normal',
					'isFinancialStuffConnected' => Auth::user()->isFinancialStuffConnected(),
					'isBackgroundOn' => Auth::user()->isBackgroundOn,
					'dailyBackgroundURL' => Auth::user()->dailyBackgroundURL(),
				)
			);
		}

		# prepare stuff for other metrics end
		#####################################################
	}
}
