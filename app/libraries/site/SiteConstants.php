<?php

/**
* --------------------------------------------------------------------------
* SiteConstants:
*       Wrapper functions for the constants.
*       All functions can be called directly from the templates
* Usage:
*       PHP     | $constant = SiteConstants::functionName();
*       BLADE   | {{ SiteConstants::functionName() }}
* --------------------------------------------------------------------------
*/
class SiteConstants {
    /* -- Class properties -- */
    private static $gridNumberOfCols  = 12;
    private static $gridNumberOfRows  = 12;
    private static $morningStartsAt   = 5;
    private static $afternoonStartsAt = 13;
    private static $eveningStartsAt   = 17;
    private static $nightStartsAt     = 22;
    private static $trialPeriodInDays = 14;
    private static $financialServices = array('braintree', 'stripe');
    private static $socialServices    = array('google', 'facebook', 'twitter');
   /**
     * ================================================== *
     *               PUBLIC STATIC SECTION                *
     * ================================================== *
     */

    /**
     * getGridNumberOfCols:
     * --------------------------------------------------
     * Returns the number of grid Y axis slots.
     * @return (integer) ($gridNumberOfCols) gridNumberOfCols
     * --------------------------------------------------
     */
    public static function getGridNumberOfCols() {
        return self::$gridNumberOfCols;
    }

    /**
     * getGridNumberOfRows:
     * --------------------------------------------------
     * Returns the number of grid X axis slots.
     * @return (integer) ($gridNumberOfRows) gridNumberOfRows
     * --------------------------------------------------
     */
    public static function getGridNumberOfRows() {
        return self::$gridNumberOfRows;
    }

    /**
     * getTimeOfTheDay:
     * --------------------------------------------------
     * Returns the time of the day string
     * @return (string) ($timeOfTheDay) morning, afternoon, evening, night
     * --------------------------------------------------
     */
    public static function getTimeOfTheDay() {
        /* Get TimeZone aware hour */
        if (Session::get('timeZone')) {
            $hour = Carbon::now(Session::get('timeZone'))->hour;
        /* Error handling (TimeZone is not present, use Server time */
        } else {
            $hour = Carbon::now()->hour;
        }

        /* Morning */
        if ((self::$morningStartsAt <= $hour) and ($hour < self::$afternoonStartsAt)) {
            return 'morning';

        /* Afternoon */
        } elseif ((self::$afternoonStartsAt <= $hour) and ($hour < self::$eveningStartsAt)) {
            return 'afternoon';

        /* Evening */
        } elseif ((self::$eveningStartsAt <= $hour) and ($hour < self::$nightStartsAt)) {
            return 'evening';

        /* Night */
        } else {
            return 'night';
        }
    }

    /**
     * getTrialPeriodInDays:
     * --------------------------------------------------
     * Returns the trial period in days.
     * @return (integer) ($trialPeriodInDays) trialPeriodInDays
     * --------------------------------------------------
     */
    public static function getTrialPeriodInDays() {
        return self::$trialPeriodInDays;
    }

    /**
     * getBraintreeErrorCodes:
     * --------------------------------------------------
     * Returns the Braintree error codes.
     * @return (array) ($errorCodes) errorCodes
     * --------------------------------------------------
     */
    public static function getBraintreeErrorCodes() {
        return [
            'Subscription has already been canceled' => 81905,
        ];
    }

    /**
     * getFinancialServices:
     * --------------------------------------------------
     * Returns the financial services.
     * @return (array) ($financialServices)
     * --------------------------------------------------
     */
    public static function getFinancialServices() {
        $services = array();
        foreach (self::$financialServices as $service) {
            array_push($services, self::getServiceMeta($service));
        }
        return $services;
    }

    /**
     * getSocialServices:
     * --------------------------------------------------
     * Returns the social services.
     * @return (array) ($socialServices)
     * --------------------------------------------------
     */
    public static function getSocialServices() {
        $services = array();
        foreach (self::$socialServices as $service) {
            array_push($services, self::getServiceMeta($service));
        }
        return $services;
    }

    /**
     * getServiceMeta:
     * --------------------------------------------------
     * Returns the specific service meta.
     * @param string $service
     * @return (array) ($serviceMeta)
     * --------------------------------------------------
     */
    private static function getServiceMeta($service) {
        return array(
            'name'             => $service,
            'display_name'     => ucfirst(str_replace('_', ' ', $service)),
            'disconnect_route' => 'service.' . $service . '.disconnect',
            'connect_route'    => 'service.' . $service . '.connect',
        );
    }


} /* SiteConstants */