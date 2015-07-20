<?php


/**
 * --------------------------------------------------------------------------
 * DashboardController: Handles the authentication related sites
 * --------------------------------------------------------------------------
 */
class DashboardController extends BaseController
{
   /**
     * Controller: getDashboard
     *
     * @return
     * @todo This function should be removed from here
     *
     */
    public function getDashboard() {
        Auth::loginUsingId(1);

        // Getting the different types of widgets.

        // Return.
        return View::make('dashboard.dashboard')
            ->with('dashboards', Auth::user()->dashboards);
    }


    /**
     * showSinglestat
     * --------------------------------------------------
     * @return
     * @todo This function should be removed from here
     * --------------------------------------------------
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

} /* DashboardController */
