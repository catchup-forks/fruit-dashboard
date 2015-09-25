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
    private static $widgetMargin      = 5;
    private static $morningStartsAt   = 5;
    private static $afternoonStartsAt = 13;
    private static $eveningStartsAt   = 17;
    private static $nightStartsAt     = 22;
    private static $trialPeriodInDays = 14;
    private static $financialServices    = array('braintree', 'stripe');
    private static $socialServices       = array('facebook', 'twitter');
    private static $webAnalyticsServices = array('google_analytics');
    private static $skipCategoriesInNotification = array('personal');
    private static $startupTypes = array(
        'SaaS'         => 'Software-as-a-service products for small and medium sized businesses.',
        'Ecommerce'    => 'Online shops selling goods to consumers.',
        'Enterprise'   => 'Products for large enterprise customers.',
        'Ads/Leadgen'  => 'Some users pay you to access the premium features.',
        'Freemium'     => 'Consumer-oriented products with freemium monetization model.',
        'Marketplaces' => 'Products that connect sellers with buyers.'
    );
    private static $chartJsColors = array(
        '77, 255, 121',
        '255, 121, 77',
        '77, 121, 255',
        '255, 77, 121',
        '210, 255, 77',
        '0, 209, 52',
        '121, 77, 255',
        '255, 210, 77',
        '77, 255, 210',
        '209, 0, 157',
    );
    private static $googleAnalyticsLaunchDate = '2005-01-01';
    private static $apiVersions = array('1.0');

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
     * getChartJsColors:
     * --------------------------------------------------
     * Returning colors for chartJS
     * @return (array) ($chartJsColors) chartJsColors
     * --------------------------------------------------
     */
    public static function getChartJsColors() {
        return self::$chartJsColors;
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
     * getWidgetMargin:
     * --------------------------------------------------
     * Returns the general widget margin.
     * @return (integer) ($widgetMargin) widgetMargin
     * --------------------------------------------------
     */
    public static function getWidgetMargin() {
        return self::$widgetMargin;
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
     * getServices
     * --------------------------------------------------
     * Returns all the services.
     * @return (array) ($services)
     * --------------------------------------------------
     */
    public static function getServices() {
        return array_merge(
                self::$socialServices,
                self::$financialServices,
                self::$webAnalyticsServices
        );
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
            'display_name'     => self::underscoreToCamelCase($service, TRUE),
            'type'             => 'service',
            'disconnect_route' => 'service.' . $service . '.disconnect',
            'connect_route'    => 'service.' . $service . '.connect',
        );
    }

    /**
     * getCustomGroupsMeta:
     * --------------------------------------------------
     * Returns the custom groups (not services).
     * @return (array) ($customGroups)
     * --------------------------------------------------
     */
    public static function getCustomGroupsMeta() {
        /* Create custom groups */
        $customGroups = array(
            array(
                'name'              => 'personal',
                'display_name'      => 'Personal',
                'type'              => 'custom',
                'disconnect_route'  => null,
                'connect_route'     => null
            ),
            array(
                'name'              => 'webhook_api',
                'display_name'      => 'API / Webhook',
                'type'              => 'custom',
                'disconnect_route'  => null,
                'connect_route'     => null
            ),
        );

        /* Return */
        return $customGroups;
    }

    /**
     * getServicesMetaByType
     * --------------------------------------------------
     * Returns the meta data from a selected service group.
     * @param  (string) ($groupname)
     * @return (array) ($financialServices)
     * --------------------------------------------------
     */
    public static function getServicesMetaByType($group) {
        /* Initialize variables */
        $services = array();
        $groupServices = array();

        /* Get the requested services */
        if      ($group == 'financial')     { $groupServices = self::$financialServices; }
        else if ($group == 'social')        { $groupServices = self::$socialServices; }
        else if ($group == 'webAnalytics')  { $groupServices = self::$webAnalyticsServices; }

        /* Build meta array */
        foreach ($groupServices as $service) {
            array_push($services, self::getServiceMeta($service));
        }

        /* Return */
        return $services;
    }

    /**
     * getAllServicesMeta:
     * --------------------------------------------------
     * Returns all the meta information from the services
     *      and custom groups.
     * @return (array) ()
     * --------------------------------------------------
     */
    public static function getAllServicesMeta() {
        /* Initialize variables */
        $services = array();

        /* Build meta array */
        foreach (self::getServices() as $service) {
            array_push($services, self::getServiceMeta($service));
        }

        /* Return */
        return $services;
    }

    /**
     * getAllGroupsMeta
     * --------------------------------------------------
     * Returns all the meta information from the services
     *      and custom groups.
     * @return (array) ()
     * --------------------------------------------------
     */
    public static function getAllGroupsMeta() {
        /* Get groups */
        $allgroups = array_merge(
            self::getCustomGroupsMeta(),
            self::getAllServicesMeta()
        );
        /* Sort by name */
        usort($allgroups, function ($a, $b) { return $b['display_name'] < $a['display_name']; });

        /* Return */
        return $allgroups;
    }

    /**
     * getWidgetDescriptorGroups:
     * --------------------------------------------------
     * Returns all widgetDescriptor groups.
     * @return (array) ($DescriptorGroups)
     * --------------------------------------------------
     */
    public static function getWidgetDescriptorGroups() {
        /* Initialize variables */
        $groups = array();

        /* Build the group array */
        foreach (self::getAllGroupsMeta() as $group) {
            /* Create array */
            array_push($groups, array(
                'name'              => $group['name'],
                'display_name'      => $group['display_name'],
                'type'              => $group['type'],
                'connect_route'     => $group['connect_route'],
                'disconnect_route'  => $group['disconnect_route'],
                'descriptors'       => WidgetDescriptor::where('category', $group['name'])
                                                            ->orderBy('name', 'asc')->get()
            ));
        }
        /* Return */
        return $groups;
    }

    /**
     * underscoreToCamelCase
     * Returning a string in CamelCase.
     * --------------------------------------------------
     * @param string $input
     * @param boolean $keepSpace
     * @return string
     * --------------------------------------------------
    */
    public static function underscoreToCamelCase($input, $keepSpace=FALSE) {
        $output = ucwords(str_replace('_',' ', $input));
        return $keepSpace ? $output : str_replace(' ', '', $output);
    }

    /**
     * getGoogleAnalyticsLaunchDate:
     * --------------------------------------------------
     * Returns the date google analytics service was launched.
     * @return (integer) ($googleAnalyticsLaunchDate)
     * --------------------------------------------------
     */
    public static function getGoogleAnalyticsLaunchDate() {
        return self::$googleAnalyticsLaunchDate;
    }

    /**
     * getApiVersions
     * --------------------------------------------------
     * Returns the available API versions.
     * @return (array) ($apiVersions) apiVersions
     * --------------------------------------------------
     */
    public static function getApiVersions() {
        return self::$apiVersions;
    }

    /**
     * getLatestApiVersion
     * --------------------------------------------------
     * Returns the latest API version.
     * @return (array) ($apiVersions) apiVersions
     * --------------------------------------------------
     */
    public static function getLatestApiVersion() {
        return end(self::$apiVersions);
    }

    /**
     * getSkippedCategoriesInNotification
     * --------------------------------------------------
     * Returns the skipped categories in notifications.
     * @return (array) ($skipCategoriesInNotification) apiVersions
     * --------------------------------------------------
     */
    public static function getSkippedCategoriesInNotification() {
        return self::$skipCategoriesInNotification;
    }

} /* SiteConstants */