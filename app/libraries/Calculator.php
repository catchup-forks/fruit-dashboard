<?php

use Abf\Event;      // needed because of conflicts with Laravel and Stripe

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class Calculator
{

    /** 
    * daily metric calculator - called from cron script
    * @param $user
    * 
    * @return null
    */

    public static function calculateMetrics($user, $time) {

        // get needed time vars
        if ($time){
            $timestamp = $time;
        }
        else {
            $timestamp = Carbon::now()->timestamp;
        }
        $today = date('Y-m-d', $timestamp);
        $yesterday = date('Y-m-d', $timestamp - 86400);

        // get today's metrics

        $metrics = Metric::firstOrNew(
            array(
                    'user'  => $user->id,
                    'date'  => $today
                    )
            );

        // get yesterday's metrics
        // this should never return an empty array

        $yesterdayMetric = Metric::where('user', $user->id)
                        ->where('date', $yesterday)
                        ->first();
        if(!$yesterdayMetric)
        {
            Calculator::calculateMetrics($user, $timestamp-86400);
            $yesterdayMetric = Metric::where('user', $user->id)
                    ->where('date', $yesterday)
                    ->first();            
        }
        // get today's events
        $events = Event::where('user', $user->id)
                    ->where('date', $today)
                    ->get();
        
        // calculate all the metrics

        // monthly recurring revenue
            // return int
        $metrics->mrr = MrrStat::calculate($yesterdayMetric->mrr, $events);

        // active users
            // return int
        $metrics->au = AUStat::calculate($yesterdayMetric->au, $events);   
        
        // annual recurring revenue
            // return int
        $metrics->arr = ArrStat::calculate($metrics->mrr);

        // average recurring revenue per active user
            // return int
        $metrics->arpu = ArpuStat::calculate($metrics->mrr, $metrics->au);
        
        // daily and monthly cancellations
            // return array
        list($daily, $monthly) = CancellationStat::calculate($events,$user);
        $metrics->cancellations = $daily;
        $metrics->monthlyCancellations = $monthly;

        // user churn
            // return float
        $metrics->uc = UserChurnStat::calculate($metrics->monthlyCancellations,$user,$timestamp);
        
        // save everything
        $metrics->save();
    }


    /** 
    * first time metric calculator - called on connect
    * calculates metrics in the past
    * can be a long running method
    * @param $user
    * 
    * @return null
    */

    public static function calculateMetricsOnConnect($user) 
    {
        $timestamp = time();
        $todayDate = date('Y-m-d', $timestamp);    

        // request and save events
        Log::info('Connecting user: '.$user->email);
        Log::info('Saving events');
        self::saveEvents($user);
        Log::info('Events saved, calculating metrics');
        // get first event date
        $firstDate = Event::where('user', $user->id)
                        ->orderBy('date','asc')
                        ->first();

        if($firstDate)
        {
            $firstDate = $firstDate->date;
        } else {
            $firstDate = date('Y-m-d', time());
        }

        // request plans and subscription infos (alternativly, customers)
        $customers = TailoredData::getCustomers($user);
        // calculate starter mrr and au
        $starterAU = count($customers);
        $starterMRR = MrrStat::calculateFirstTime($customers);
        
        // reverse calculate from events
        $historyMRR = MrrStat::calculateHistory($timestamp,$user,$firstDate,$starterMRR);
        $historyAU = AUStat::calculateHistory($timestamp,$user,$firstDate,$starterAU);
        $historyCancellation = CancellationStat::calculateHistory($timestamp,$user);
        // calculate arr, arpu, uc
        $historyUC = UserChurnStat::calculateHistory($historyCancellation['monthly'], $historyAU);
        $historyARR = ArrStat::calculateHistory($historyMRR);
        $historyARPU = ArpuStat::calculateHistory($historyMRR, $historyAU);

        // save all the data
        foreach ($historyAU as $date => $au) 
        {
            $metrics = Metric::firstOrNew(
                array(
                        'date'      => $date,
                        'user'      => $user->id
                    )
                );
            $metrics->user = $user->id;
            $metrics->date = $date;

            $metrics->mrr = $historyMRR[$date];
            $metrics->au = $au;
            $metrics->arr = $historyARR[$date];
            $metrics->arpu = $historyARPU[$date];
            $metrics->uc = array_key_exists($date, $historyUC) ? $historyUC[$date] : null;

            $metrics->cancellations = array_key_exists($date, $historyCancellation['daily']) 
                                                ? $historyCancellation['daily'][$date]
                                                : 0 ;
            $metrics->monthlyCancellations = array_key_exists($date, $historyCancellation['monthly']) 
                                                ? $historyCancellation['monthly'][$date]
                                                : 0 ;

            $metrics->save();
        }
        Log::info('Calculations done');
    }

    /**
    * save every event we don't have already
    *
    * @param user
    *
    * @return null
    */

    public static function saveEvents($user)
    {

        $widgets = $user->dashboards()->first()->widgets()->get();
        foreach ($widgets as $widget) {

            # create class name, f.e. stripe --> StripeHelper
            $widgetClassName = ucfirst($widget->widget_provider).'Helper';

            # method name
            $widgetMethodName = 'getEvents';

            # check if class & method exists, f.e. StripeHelper::createDashboardData
            if(class_exists($widgetClassName) && method_exists($widgetClassName, $widgetMethodName)){

                # it does, create dashboard data
                $widgetClassName::$widgetMethodName($user, $widget);

            } else {}
   
        }

        ###############################################
        # stripe & braintree & paypal stuff start

        $eventsToSave = TailoredData::getEvents($user);

        if($eventsToSave)
        {
            foreach ($eventsToSave as $id => $event) {
                // check, if we already saved this event
                $newEvent = Event::firstOrNew(
                    array(
                        'date'      => date('Y-m-d', $event['created']),
                        'eventID'   => $id
                    )
                );
                $newEvent->date                 = date('Y-m-d', $event['created']);
                $newEvent->eventID              = $id;
                $newEvent->user                 = $user->id;
                $newEvent->created              = date('Y-m-d H:i:s', $event['created']);
                $newEvent->provider             = $event['provider'];
                $newEvent->type                 = $event['type'];
                $newEvent->object               = json_encode($event['data']['object']);
                $newEvent->previousAttributes   = isset($event['data']['previous_attributes'])
                                                    ? json_encode($event['data']['previous_attributes'])
                                                    : null;
                $newEvent->save();
            }
        }

        # stripe & braintree & paypal stuff end
        ###############################################

    }

    /**
    * helper function for calculationg MRR
    * @param plan array
    *
    * @return int, contribution to MRR
    */

    public static function getMRRContribution($plan)
    {
        // check all possible intervals, and return the correct amount

        switch ($plan['interval']) {

            case 'day':
                return $plan['amount'] * 30;        // average days in a month

            case 'week':
                return $plan['amount'] * 4;         // average weeks in a month
            
            case 'month':
                return $plan['amount'];             // basic amount

            case 'year':
                return round($plan['amount'] / 12); // rebased for a month

            default:
                return null;                        // should never ever return this, its a major fault at provider
        }
    }

    /*
    |--------------------------------------------------------------
    | Base functions (depend only on data from Stripe/Paypal/Other)
    |--------------------------------------------------------------
    */


    /**
    * OR - Other Revenues
    * every revenue, which not connected to recurring revenues
    *
    * Required events/data:
    *   - charge events
    *
    * @return int (cents)
    */


    /**
    * Refunds
    * refunds on any made charge
    *
    * Required events/data:
    *   - refund events
    *
    * @return int (cents)
    */


    /**
    * Fees
    * all payed fees
    *
    * Required events/data:
    *   - all events regarding spending money
    *
    * @return int (cents)
    */


    /**
    * Downgrade
    * changing to a smaller plan
    *
    * Required events/data:
    *   - update events, where plan cost went down
    *
    * Must save also:
    *   - the price difference
    *
    * @return int (pieces)
    */


    /**
    * Upgrades
    * changing to a bigger plan
    *
    * Required events/data:
    *   - update events, where plan cost went up
    *
    * @return int (pieces)
    */


    /**
    * CR - Coupons Redeemed
    * income loss due to redeemed coupons
    *
    * Required events/data
    *   - Coupon/Disount data
    *
    * @return int (cents)
    */


    /**
    * FC - Failed Charges
    *
    * Required events/data
    *   - charge events, which failed to be paid
    *
    * @return int (cents)
    */


    /*
    |--------------------------------------------------------------
    | Derived functions (have dependecies on base functions)
    |--------------------------------------------------------------
    */


    /**
    * NR - net revenue
    * MRR + OR - refunds
    *
    * Required functions:
    *   -
    *   - MRR
    *   - Refunds
    * ORR
    * @return int (cents)
    */


    /**
    * LV - Lifetime Value
    * the average 'usefullness' of users
    * (Average Revenue Per User) / (User Churn)
    *
    * Required functions:
    *   - ARPU
    *   - UC
    *
    * @return int (cents/percent)
    */


    /**
    * RC - Revenue Churn
    * (MRR loss due to cancellations and downgrades) / (last month MRR) * 100
    *
    * Required functions:
    *   - MRR (30 days before)
    *   - price difference of cancellations
    *   - price difference of downgrades
    *
    * @return int (percent)
    */


    /*
    |------------------------------------------------------------
    | Other helper functions
    |------------------------------------------------------------
    */


    /**
    * Format events to be displayed on dashboard
    * @param user object
    *
    * @return array of formatted events
    */

    public static function formatEvents($user)
    {
        $eventArray = array();      // return array
        $tempArray = array();       // helper
        $prevTempArray = array();   // previous values

        // last X events from database
        // select only those event types, which we show on dashboard
        $events = Event::where('user', $user->id)
                ->whereIn('type', ['charge.succeeded'
                                    ,'charge.failed'
                                    ,'charge.captured'
                                    ,'charge.refunded'
                                    ,'customer.created'
                                    ,'customer.deleted'
                                    ,'customer.subscription.created'
                                    ,'customer.subscription.updated'
                                    ,'customer.subscription.deleted'
                                    ,'customer.discount.created'
                                    ,'customer.discount.updated'
                                    ,'customer.discount.deleted'])
                ->orderBy('created', 'desc')
                ->take(20)
                ->get();


        $i = 0;
        
        foreach ($events as $id => $event)
        {
            if($event->provider != 'connect')
            {
                try
                {

                    // decoding object
                    $tempArray = json_decode(strstr($event->object, '{'), true);
                    $prevTempArray = !is_null($event->previousAttributes)
                                        ? json_decode(strstr($event->previousAttributes , '{'), true)
                                        : null;

                    // formatting and creating data for return array
                    // type eg. 'charge.succeeded'
                    $eventArray[$i]['type'] = $event->type;
                    // provider eg. 'stripe'
                    $eventArray[$i]['provider'] = $event->provider;
                    // date eg. '02-11 20:44'
                    $eventArray[$i]['date'] = date('m-d H:i', strtotime($event->created));

                    // name eg. 'chris'
                    if (array_key_exists('card', $tempArray)){
                        if(array_key_exists('name', $tempArray['card'])){
                            if($tempArray['card']['name']){
                                $eventArray[$i]['name'] = $tempArray['card']['name'];
                            }
                            else {
                                $eventArray[$i]['name'] = 'Someone';
                            }
                        }
                        else {
                            $eventArray[$i]['name'] = 'Another guy';
                        }
                    }
                    else {
                        $eventArray[$i]['name'] = 'Some company';
                    }
                    // currency
                    if (array_key_exists('currency', $tempArray)){
                        $eventArray[$i]['currency'] = $tempArray['currency'];
                    }
                    elseif (array_key_exists('plan', $tempArray)){
                        if (array_key_exists('currency', $tempArray['plan'])){
                            $eventArray[$i]['currency'] = $tempArray['plan']['currency'];
                        }
                    }
                    else {
                        $eventArray[$i]['currency'] = null;
                    }
                    
                    // amount paid
                    if(array_key_exists('amount', $tempArray)){
                        $eventArray[$i]['amount'] = $tempArray['amount'];
                    }
                    elseif (array_key_exists('amount_due', $tempArray)){
                        $eventArray[$i]['amount'] = $tempArray['amount_due'];
                    }
                    elseif (array_key_exists('plan', $tempArray)){
                        if (array_key_exists('amount', $tempArray['plan'])){
                            $eventArray[$i]['amount'] = $tempArray['plan']['amount'];
                        }
                    }
                    elseif (array_key_exists('amount_refunded', $tempArray)){
                        $eventArray[$i]['amount'] = $tempArray['amount_refunded'];
                    }
                    else {
                        $eventArray[$i]['amount'] = null;
                    }
                    // plan name
                    if (array_key_exists('plan', $tempArray)){
                        if (array_key_exists('name', $tempArray['plan'])){
                            $eventArray[$i]['plan_name'] = $tempArray['plan']['name'];
                        }
                    }
                    // plan interval
                    if (array_key_exists('plan', $tempArray))
                    {
                    switch ($tempArray['plan']['interval']) 
                        {
                            case 'day':
                                $eventArray[$i]['plan_interval'] = 'daily';
                                break;
                            case 'week':
                                $eventArray[$i]['plan_interval'] = 'weekly';
                                break;
                            case 'month':
                                $eventArray[$i]['plan_interval'] = 'monthly';
                                break;
                            case 'year':
                                $eventArray[$i]['plan_interval'] = 'yearly';
                                break;
                            default:
                                // don't do anything
                        }
                    }
                    // previous plan ID, name, interval and amount
                    if (!is_null($prevTempArray))
                    {
                        if (array_key_exists('plan', $prevTempArray))
                        {
                            $eventArray[$i]['prevPlanID'] = $prevTempArray['plan']['id'];
                            $eventArray[$i]['prevPlanName'] = $prevTempArray['plan']['name'];
                            $eventArray[$i]['prevPlanAmount'] = $prevTempArray['plan']['amount'];

                            switch ($prevTempArray['plan']['interval']) 
                            {
                                case 'day':
                                    $eventArray[$i]['prevPlanInterval'] = 'daily';
                                    break;
                                case 'week':
                                    $eventArray[$i]['prevPlanInterval'] = 'weekly';
                                    break;
                                case 'month':
                                    $eventArray[$i]['prevPlanInterval'] = 'daily';
                                    break;
                                case 'year':
                                    $eventArray[$i]['prevPlanInterval'] = 'yearly';
                                    break;
                                default:
                                    // don't do anything
                            }
                        }
                    }

                    // if the event is a coupon event

                    if (isset($tempArray['coupon']))
                    {
                        $eventArray[$i]['newCoupon'] = $tempArray['coupon']['id'];

                        if (!is_null($prevTempArray)) 
                        {
                            $eventArray[$i]['prevCoupon'] = $prevTempArray['coupon']['id'];
                        }
                    }
                    $i++;
                } catch (Exception $e)
                {
                    
                }
            } // /if - provider
        } // /foreach - events
        return $eventArray;
    }


    /** 
    * Stores all the metrics we currently calculate
    *
    * @return assoc array of metric Classnames and column headers
    */

    public static function currentMetrics()
    {

        // on the dashboard the metrics are shown 
        // in the same order as this array
        
        return array(
            'mrr'               => array('metricClass'          => 'MrrStat'
                                        ,'metricName'           => 'Monthly Recurring Revenue'
                                        ,'metricDescription'    => 'MRR is a normalized measurement of recurring revenue, most frequently measured with a constant value in each month of the subscription period.')
            
            ,'au'               => array('metricClass'          => 'AUStat'
                                        ,'metricName'           => 'Active Users'
                                        ,'metricDescription'    => 'The number of customers who have at least one subscription.')
            
            ,'arr'              => array('metricClass'          => 'ArrStat'
                                        ,'metricName'           => 'Annual Recurring Revenue'
                                        ,'metricDescription'    => 'ARR is the value of the contracted recurring revenue components of your term subscriptions normalized to a one year period.')
            
            ,'arpu'             => array('metricClass'          => 'ArpuStat'
                                        ,'metricName'           => 'Average Revenue Per User'
                                        ,'metricDescription'    => 'ARPU is defined as the total revenue divided by the number of subscribers.')
            
            ,'cancellations'    => array('metricClass'          => 'CancellationStat'
                                        ,'metricName'           => 'Cancellations'
                                        ,'metricDescription'    => 'The amount of how many subscriptions were cancelled, or not renewed.')
            
            ,'uc'               => array('metricClass'          => 'UserChurnStat'
                                        ,'metricName'           => 'User Churn'
                                        ,'metricDescription'    => 'The percentage of subscribers to a service that discontinue their subscription to that service in a month.')
        );
    }
}
