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

    /* Gridster */
    private static $gridNumberOfCols  = 12;
    private static $gridNumberOfRows  = 12;
    private static $widgetMargin      = 5;

    /* Greetings widget */
    private static $morningStartsAt   = 5;
    private static $afternoonStartsAt = 13;
    private static $eveningStartsAt   = 17;
    private static $nightStartsAt     = 22;

    /* Trial period */
    private static $trialPeriodInDays = 14;

    /* Signup wizard */
    private static $signupWizardSteps = array(
        'company-info',
        'google-analytics-connection',
        //'google-analytics-profile',
        //'google-analytics-select-goal',
        'social-connections',
        'financial-connections',
        'finished',
    );

    private static $signupWizardStartupTypes = array(
        'SaaS'         => 'SaaS | Software-as-a-service products for small and medium sized businesses.',
        'Ecommerce'    => 'Ecommerce | Online shops selling goods to consumers.',
        'Enterprise'   => 'Enterprise | Products for large enterprise customers.',
        'Ads/Leadgen'  => 'Ads/Leadgen | Some users pay you to access the premium features.',
        'Freemium'     => 'Freemium | Consumer-oriented products with freemium monetization model.',
        'Marketplaces' => 'Marketplaces | Products that connect sellers with buyers.',
        'Other'        => 'Other'
    );

    private static $signupWizardCompanySize = array(
        '1-5'    => '1-5',
        '5-10'   => '5-10',
        '10-20'  => '10-20',
        '20-50'  => '20-50',
        '50-100' => '50-100',
        '100+'   => '100+'
    );

    private static $signupWizardCompanyFunding = array(
        'Bootstrapped'            => 'Bootstrapped',
        'Incubator / Accelerator' => 'Incubator / Accelerator',
        'Angel'                   => 'Angel',
        'Seed'                    => 'Seed',
        'Series A'                => 'Series A',
        'Series B'                => 'Series B',
        'Series C'                => 'Series C',
        'Other'                   => 'Other'
    );

    /* Auto dashboards */
    private static $autoDashboards    = array(
        'Acquisition' => array(
            array(
                'type'     => 'google_analytics_sessions',
                'position' => '{"row":1,"col":1,"size_x":4,"size_y":5}',
                'settings' => array('type' => 'chart'),
                'pic_url'  => 'img/demonstration/promo/un_visitor_chart.png'
            ),
            array(
                'type'     => 'google_analytics_sessions',
                'position' => '{"row":1,"col":6,"size_x":4,"size_y":5}',
                'settings' => array('type' => 'table', 'length' => 5),
                'pic_url'  => 'img/demonstration/promo/un_visitor_table.png'
            ),
        ),
        'Activation' => array(
            array(
                'type'     => 'facebook_likes',
                'position' => '{"col":1,"row":1,"size_x":5,"size_y":5}',
                'settings' => array('type' => 'chart'),
                'pic_url'  => 'img/demonstration/promo/fb_likes_chart.png'
            ),
            array(
                'type'     => 'twitter_followers_count',
                'position' => '{"col":6,"row":1,"size_x":2,"size_y":2}',
                'settings' => array('period' => 'days', 'multiplier' => 1),
                'pic_url'  => 'img/demonstration/promo/tw_followers_count.png'
            ),
            array(
                'type'     => 'twitter_mentions',
                'position' => '{"col":8,"row":1,"size_x":5,"size_y":8}',
                'settings' => array('count' => 5),
                'pic_url'  => 'img/demonstration/promo/tw_mentions.png'
            ),
        ),
        'Retention' => array(),
        'Revenue' => array(),
        //'Referral' => array()
    );

    /* Services and connections */
    private static $financialServices    = array('braintree', 'stripe');
    private static $socialServices       = array('facebook', 'twitter');
    private static $webAnalyticsServices = array('google_analytics');
    private static $facebookPopulateDataDays = 60;
    private static $googleAnalyticsLaunchDate = '2005-01-01';

    /* Notifications */
    private static $skipCategoriesInNotification = array('personal');
    private static $slackColors = array(
        '#BADA55',
        '#ABCDE',
        '#FFBB66',
    );


    /* Graphs and measures stat */
    private static $singleStatHistoryDiffs = array(
        'days'   => array(1, 7, 30),
        'weeks'  => array(1, 4, 12),
        'months' => array(1, 3, 6),
        'years'  => array(1, 3, 5),
    );

    private static $chartJsColors = array(
        '105 ,153, 209',
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

    /* API */
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
     * getSlackColors:
     * --------------------------------------------------
     * Returning colors for slack
     * @return (array) ($slackColors) slackColors
     * --------------------------------------------------
     */
    public static function getSlackColors() {
        return self::$slackColors;
    }

    /**
     * getSlackColor:
     * --------------------------------------------------
     * Returning the corresponging color
     * @param int $i
     * @return string
     * --------------------------------------------------
     */
    public static function getSlackColor($i) {
        if (is_int($i)) {
            return self::$slackColors[($i) % count(self::$slackColors)];
        }
    }

    /**
     * getSingleStatHistoryDiffs:
     * --------------------------------------------------
     * Returning the single stat diffs
     * @return (array) ($singleStatHistoryDiffs)
     * --------------------------------------------------
     */
    public static function getSingleStatHistoryDiffs() {
        return self::$singleStatHistoryDiffs;
    }

    /**
     * getSignupWizardStep:
     * --------------------------------------------------
     * Returns the next step for the signup wizard
     * @param (string) ($index) one of: next, prev, first, last
     * @param (string) ($currentStep) the current step or null
     * @return (string) ($nextStep) the next step
     * --------------------------------------------------
     */
    public static function getSignupWizardStep($index, $currentStep) {
        /* First or last step */
        if ($index == 'first') {
            return self::$signupWizardSteps[0];
        } elseif ($index == 'last') {
            return end(self::$signupWizardSteps);
        }

        /* Find current step */
        if (in_array($currentStep, self::$signupWizardSteps)) {
            $i = array_search($currentStep, self::$signupWizardSteps);
        /* Current step cannot be found */
        } else {
            return null;
        }
        
        /* Return step and check overflows */
        if ($index == 'next') {
            if ($i < count(self::$signupWizardSteps)-1) {
                return self::$signupWizardSteps[$i+1];
            } else {
                return null;
            }
        } elseif ($index == 'prev') {
            if ($i < 1) {
                return self::$signupWizardSteps[0];
            } else {
                return self::$signupWizardSteps[$i-1];
            }
        }
    }

    /**
     * getSignupWizardStartupTypes:
     * --------------------------------------------------
     * Returns the startup types
     * @return (array) ($signupWizardStartupTypes) startupTypes
     * --------------------------------------------------
     */
    public static function getSignupWizardStartupTypes() {
        return self::$signupWizardStartupTypes;
    }

    /**
     * getSignupWizardCompanySize:
     * --------------------------------------------------
     * Returns the startup types
     * @return (array) ($signupWizardCompanySize) CompanySize
     * --------------------------------------------------
     */
    public static function getSignupWizardCompanySize() {
        return self::$signupWizardCompanySize;
    }

    /**
     * getSignupWizardCompanyFunding:
     * --------------------------------------------------
     * Returns the startup types
     * @return (array) ($signupWizardCompanyFunding) CompanyFunding
     * --------------------------------------------------
     */
    public static function getSignupWizardCompanyFunding() {
        return self::$signupWizardCompanyFunding;
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
    public static function getServiceMeta($service) {
        return array(
            'name'             => $service,
            'display_name'     => Utilities::underscoreToCamelCase($service, TRUE),
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
                'display_name'      => 'Webhook / API',
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
     * getGoogleAnalyticsLaunchDate:
     * --------------------------------------------------
     * Returns the date google analytics service was launched.
     * @return (integer) ($googleAnalyticsLaunchDate)
     * --------------------------------------------------
     */
    public static function getGoogleAnalyticsLaunchDate() {
        return Carbon::createFromFormat('Y-m-d', self::$googleAnalyticsLaunchDate);
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
     * getAutoDashboards
     * --------------------------------------------------
     * Returns the startup metrics.
     * @return (array) ($autoDashboards) autoDashboards
     * --------------------------------------------------
     */
    public static function getAutoDashboards() {
        return self::$autoDashboards;
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
     * cleanupPolicy
     * --------------------------------------------------
     * Returns whether or not to delete the hourly data.
     * @param $entryTime
     * @return boolean: TRUE->keep FALSE->delete
     * --------------------------------------------------
     */
    public static function cleanupPolicy($entryTime) {
        return $entryTime->diffInWeeks(Carbon::now(), FALSE) < 2;
    }

    /**
     * getServicePopulationPeriod
     * --------------------------------------------------
     * Returns how many days back should the populator go.
     * @return (int) ($facebookPopulateDataDays)
     * --------------------------------------------------
     */
    public static function getServicePopulationPeriod() {
        return array(
            'facebook'         => 60,
            'google_analytics' => 60,
            'twitter'          => null,
            'stripe'           => 30,
            'braintree'        => 30,
        );
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