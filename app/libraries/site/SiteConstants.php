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

    /* Trial period */
    private static $trialPeriodInDays = 21;

    /* Signup wizard */
    private static $signupWizardSteps = array(
        'company-info'          => 'company-info',
        'google-analytics'      => array(
            'google-analytics-connection',
            'google-analytics-profile',
            'google-analytics-goal',
        ),
        'social-connections'    => 'social-connections',
        'financial-connections' => 'financial-connections',
        'slack-integration'     => 'slack-integration',
        'install-extension'     => 'install-extension',
        'finished'              => 'finished',
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

    /* Dashboard cache */
    private static $dashboardCacheMinutes = 60;

    /* Widget layouts. */
    const LAYOUT_COMBINED_BAR_LINE = 'combined-bar-line';
    const LAYOUT_TABLE             = 'table';
    const LAYOUT_COUNT             = 'count';
    const LAYOUT_MULTI_LINE        = 'multi-line';
    const LAYOUT_SINGLE_LINE       = 'single-line';

    /* Velocities. */
    private static $velocities = array(
        'daily'  => 'days',
        'weekly' => 'weeks',
        'monthy' => 'months',
        'yearly' => 'years'
    );

    /* Auto dashboards */
    private static $autoDashboards = array(
        'Big Picture' => array(
            array(
                'type'     => 'google_analytics_users',
                'position' => '{"row":1,"col":1,"size_x":5,"size_y":6}',
                'settings' => array(
                    'type' => self::LAYOUT_COMBINED_BAR_LINE,
                    'length' => 5
                ),
                'pic_url'  => 'img/demonstration/promo/unique-visitors-chart.png'
            ),
            array(
                'type'     => 'google_analytics_goal_completion',
                'position' => '{"row":1,"col":6,"size_x":5,"size_y":6}',
                'settings' => array(
                    'type' => self::LAYOUT_COMBINED_BAR_LINE,
                    'length' => 5
                ),
                'pic_url'  => 'img/demonstration/promo/goal-completions-chart.png'
            ),
            array(
                'type'     => 'facebook_likes',
                'position' => '{"row":7,"col":1,"size_x":5,"size_y":6}',
                'settings' => array(
                    'type' => self::LAYOUT_COMBINED_BAR_LINE,
                    'length' => 5
                ),
                'pic_url'  => 'img/demonstration/promo/facebook-likes-chart.png'
            ),
            array(
                'type'     => 'twitter_followers',
                'position' => '{"row":7,"col":5,"size_x":5,"size_y":6}',
                'settings' => array(
                    'type' => self::LAYOUT_COUNT,
                    'length' => 1
                ),
                'pic_url'  => 'img/demonstration/promo/twitter-followers-count.png'
            ),
        ),
        //'Referral' => array()
    );
    private static $sharedWidgetsDashboardName = 'Shared widgets';

    /* Services and connections */
    private static $financialServices    = array('braintree', 'stripe');
    private static $socialServices       = array('facebook', 'twitter');
    private static $webAnalyticsServices = array('google_analytics');
    private static $facebookPopulateDataDays  = 60;
    private static $googleAnalyticsLaunchDate = '2005-01-01';

    /* Notifications */
    private static $skipCategoriesInNotification = array(
        'personal',
        'hidden'
    );

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
     * getChartJsColors:
     * --------------------------------------------------
     * Return colors for chartJS
     * @return (array) ($chartJsColors) chartJsColors
     * --------------------------------------------------
     */
    public static function getChartJsColors() {
        return self::$chartJsColors;
    }

    /**
     * getSlackColors:
     * --------------------------------------------------
     * Return colors for slack
     * @return (array) ($slackColors) slackColors
     * --------------------------------------------------
     */
    public static function getSlackColors() {
        return self::$slackColors;
    }

    /**
     * getSlackColor:
     * --------------------------------------------------
     * Return the corresponging color
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
     * Return the single stat diffs
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
     * @param (string)  ($index) one of: next, prev, first, last
     * @param (boolean) ($to_group) true: jumps to next group, false jumps to next item
     * @param (string) ($currentStep) the current step or empty string
     * @return (string) ($nextStep) the next step
     * --------------------------------------------------
     */
    public static function getSignupWizardStep($index, $currentStep='', $to_group=false) {
        /* First or last step */
        if ($index == 'first') {
            return array_values(self::$signupWizardSteps)[0];
        } elseif ($index == 'last') {
            return end(self::$signupWizardSteps);
        }

        /* Find current step */
        $groupidx  = null;
        $itemidx   = null;
        $keys = array_keys(self::$signupWizardSteps);
        $i=0;
        foreach (self::$signupWizardSteps as $group => $items) {
            if ($currentStep == $group) {
                $groupidx = $i;
                break;
            } elseif (is_array($items) and in_array($currentStep, $items)) {
                $groupidx = $i;
                $itemidx  = array_search($currentStep, $items);
                break;
            }
            $i += 1;
        }

        /* Current step cannot be found */
        if (is_null($groupidx)) {
            return null;
        }

        /* Return next step */
        if ($index == 'next') {
            /* Return next sub-group element */
            if (is_numeric($itemidx) and ($itemidx < count(self::$signupWizardSteps[$keys[$groupidx]])-1) and (!$to_group)) {
                return self::$signupWizardSteps[$keys[$groupidx]][$itemidx+1];
            /* Next group has sub-group, return sub-group first */
            } else if (is_array(self::$signupWizardSteps[$keys[$groupidx+1]])) {
                return self::$signupWizardSteps[$keys[$groupidx+1]][0];
            /* Return next group */
            } else {
                return self::$signupWizardSteps[$keys[$groupidx+1]];
            }

        /* Return previous step */
        } elseif ($index == 'prev') {
            /* Return prev sub-group element */
            if (is_numeric($itemidx) and ($itemidx > 0) and (!$to_group)) {
                return self::$signupWizardSteps[$keys[$groupidx]][$itemidx-1];
            /* Prev group has sub-group, return sub-group element */
            } else if (is_array(self::$signupWizardSteps[$keys[$groupidx-1]])) {
                // Return first from group
                if ($to_group) {
                    return self::$signupWizardSteps[$keys[$groupidx-1]][0];
                // Return last from group
                } else {
                    return end(self::$signupWizardSteps[$keys[$groupidx-1]]);
                }
            /* Return prev group */
            } else {
                return self::$signupWizardSteps[$keys[$groupidx-1]];
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
            'display_name'     => Utilities::underscoreToCamelCase($service, true),
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
     * @return boolean: true->keep false->delete
     * --------------------------------------------------
     */
    public static function cleanupPolicy($entryTime) {
        return $entryTime->diffInWeeks(Carbon::now(), false) < 2;
    }

    /**
     * getSharedWidgetsDashboardName
     * --------------------------------------------------
     * Returns The shared widgets dashboard's name
     * @return string
     * --------------------------------------------------
     */
    public static function getSharedWidgetsDashboardName() {
        return self::$sharedWidgetsDashboardName;
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
            'facebook'         => 90,
            'google_analytics' => 120,
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

    /**
     * getDashboardCacheMinutes
     * Returns the number of minutes, of how long the
     * dashboard should be stored in the cache.
     */
    public static function getDashboardCacheMinutes() {
        return self::$dashboardCacheMinutes;
    }

    /**
     * getVelocities:
     * --------------------------------------------------
     * Return the velocities for the site.
     * @return (array) ($velocities) 
     * --------------------------------------------------
     */
    public static function getVelocities() {
        return self::$velocities;
    }

} /* SiteConstants */
