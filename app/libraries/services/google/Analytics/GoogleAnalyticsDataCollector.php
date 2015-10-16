<?php

/**
* --------------------------------------------------------------------------
* GoogleAnalyticsDataCollector:
*       Getting data from google account.
* --------------------------------------------------------------------------
*/

class GoogleAnalyticsDataCollector
{
    /**
     * The user object.
     *
     * @var User
     */
    private $user;

    /**
     * The client object.
     *
     * @var Google_Client
     */
    private $client;

    /**
     * The analytics object.
     *
     * @var Google_Service_Analytics
     */
    private $analytics;

    /* -- Constructor -- */
    function __construct($user, $connector=null) {
        $this->user = $user;
        if (is_null($connector)) {
            $connector = new GoogleAnalyticsConnector($user);
            $connector->connect();
        }
        $this->client = $connector->getClient();
        $this->analytics = new Google_Service_Analytics($this->client);
    }

    /**
     * saveProperties
     * Saves a user's google analytics properties.
     * --------------------------------------------------
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function saveProperties() {
        $properties = array();
        foreach ($this->getAccountIds() as $accountId) {
            /* Gathering data from google */
            $ga_properties = $this->analytics->management_webproperties->listManagementWebproperties($accountId);
            $items = $ga_properties->getItems();
            if (count($items) <= 0) {
                continue;
            }
            foreach ($items as $item) {
                /* Saving properties */
                $propertyId = $item->getId();
                $property = new GoogleAnalyticsProperty(array(
                    'name'        => $item->getName(),
                    'account_id'  => $accountId,
                    'property_id' => $propertyId
                ));
                $property->user()->associate($this->user);
                array_push($properties, $property);
            }
        }
        if (count($properties) > 0) {
            /* Only refreshing if we have results. */
            $this->user->googleAnalyticsProperties()->delete();
            foreach ($properties as $property) {
                $property->save();
                /* Saving profiles */
                $this->saveProfiles($property);
                $this->saveGoals($property);
            }
        }
        return $properties;
    }

    /**
     * saveGoals
     * Saves a user's google analytics goals.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * --------------------------------------------------
     */
    private function saveGoals($property) {
        /* Gathering data from google */
        $analyticsGoals = $this->getGoals($property);
        foreach ($analyticsGoals as $iGoal) {
            $profile = $this->user->googleAnalyticsProfiles()
                ->where('profile_id', $iGoal->getProfileId())
                ->first(array('google_analytics_profiles.id'));
            if (is_null($profile)) {
                continue;
            }
            /* Saving goal. */
            $goal = new GoogleAnalyticsGoal(array(
                'name'    => $iGoal->getName(),
                'goal_id' => $iGoal->getId(),
            ));
            $goal->profile()->associate($profile);
            $goal->save();
        }
    }

    /**
     * saveProfiles
     * Saves a user's google analytics profiles.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * --------------------------------------------------
     */
    private function saveProfiles($property) {
        /* Gathering data from google */
        $analyticsProfiles = $this->getProfiles($property);
        foreach ($analyticsProfiles as $iProfile) {
            /* Saving profile. */
            $profile = new GoogleAnalyticsProfile(array(
                'name'       => $iProfile->getName(),
                'profile_id' => $iProfile->getId(),
            ));
            $profile->property()->associate($property);
            $profile->save();
        }
    }

    /**
     * getMetrics
     * Retrieving specific metrics for the selected property.
     * --------------------------------------------------
     * @param GoogleAnalyticsProfile $profileId
     * @param string $start
     * @param string $end
     * @param array $metrics
     * @param array $optParams
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getMetrics($profileId, $start, $end, array $metrics, array $optParams=array()) {
        $useDimensions = array_key_exists('dimensions', $optParams);
        $metricsData = array();

        try {
            /* Retrieving results from API */
            $results = $this->analytics->data_ga->get('ga:' . $profileId, $start, $end, 'ga:' . implode(',ga:', $metrics), $optParams);
        } catch (ServiceException $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }

        /* Getting rows. */
        $rows = $results->getRows();

        if (count($rows) > 0) {
            /* Populating metricsData. */
            if ($useDimensions) {
                $metricsData = $this->buildDimensionsData($metrics, $rows);
            } else {
                $metricsData = $this->buildSimpleMetricsData($metrics, $rows);
            }
        } else if( ! $useDimensions) {
            /* The value is 0, for all the data. */
            $rows = array();
            foreach ($metrics as $metric) {
                array_push($rows, 0);
            }
            $metricsData = $this->buildSimpleMetricsData($metrics, array($rows));
        }
        return $metricsData;
    }

    /**
     * buildSimpleMetricsData
     * Building dimension specific data.
     * --------------------------------------------------
     * @param array $metrics
     * @param array  $rows
     * @return array
     * --------------------------------------------------
    */
    private function buildSimpleMetricsData($metrics, $rows) {
        /* Creating metrics array. */
        $metricsData = array();
        $i = 0;
        foreach ($metrics as $metric) {
            $metricsData[$metric] = $rows[0][$i++];
        }
        return $metricsData;
    }

    /**
     * buildDimensionsData
     * Building dimension specific data.
     * --------------------------------------------------
     * @param array $metrics
     * @param array $rows
     * @return array
     * --------------------------------------------------
    */
    private function buildDimensionsData($metrics, $rows) {
        /* Creating metrics array. */
        $metricsData = array();
        foreach ($metrics as $metric) {
            $metricsData[$metric] = array();
        }

        $i = 1;
        foreach ($metrics as $metric) {
            foreach ($rows as $row) {
                if ( ! array_key_exists($row[0], $metricsData[$metric])) {
                    $metricsData[$metric][$row[0]] = array();
                }
                if (count($row) == 3) {
                    /* Multiple values on the row. one metric */
                    $metricsData[$metric][$row[0]][$row[1]] = $row[2];
                } else {
                    /* One data per row. multiple metrics */
                    $metricsData[$metric][$row[0]] = $row[$i];
                }
            }
            ++$i;
        }
        return $metricsData;
    }

    /**
     * getAvgSessionDuration
     * Returning the number of sessions.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getAvgSessionDuration($profileId) {
        return $this->getMetrics(
            $profileId, 'today', 'today', array('avgSessionDuration')
        )['avgSessionDuration'];
   }

    /**
     * getSessionsPerUser
     * Returning the number of sessions per user.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getSessionsPerUser($profileId) {
        return $this->getMetrics(
            $profileId, 'today', 'today', array('sessionsPerUser')
        )['sessionsPerUser'];
   }

    /**
     * getSessions
     * Returning the number of sessions.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getSessions($profileId) {
        return $this->getMetrics(
            $profileId,
            SiteConstants::getGoogleAnalyticsLaunchDate()->toDateString(),
            'today', array('sessions')
        )['sessions'];
   }

    /**
     * getUsers
     * Returning the number of users.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getUsers($profileId) {
        return $this->getMetrics(
            $profileId,
            SiteConstants::getGoogleAnalyticsLaunchDate()->toDateString(),
            'today', array('users')
        )['users'];
   }

    /**
     * getGoalCompletions
     * Returning the number of goal completions.
     * --------------------------------------------------
     * @param string $profileId
     * @param string $goalId
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getGoalCompletions($profileId, $goalId) {
        $metricName = 'goal' . $goalId . 'Completions';
        return $this->getMetrics(
            $profileId,
            SiteConstants::getGoogleAnalyticsLaunchDate()->toDateString(),
            'today', array($metricName)
        )[$metricName];
   }

    /**
     * getBounceRate
     * Returning the percentage of bounce rate.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getBounceRate($profileId) {
        return $this->getMetrics(
            $profileId,
            'today', 'today', array('bounceRate')
        )['bounceRate'];
   }

    /**
     * getActiveUsers
     * Returning the active useres (multiple).
     * --------------------------------------------------
     * @param string $profileId
     * @param $metricNames
     * @param array $optionalParams
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function getActiveUsers($profileId, array $metricNames, array $optionalParams=array()) {
        $currentValues = array();
        foreach ($metricNames as $metric) {
            $currentValues[$metric] = array_values($this->getMetrics(
                $profileId,
                'today', 'today',
                array($metric), $optionalParams
            )[$metric])[0];
        }
        return $currentValues;
   }

    /**
     * getProfiles
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    private function getProfiles($property) {
        try {
            return $this->analytics->management_profiles->listManagementProfiles($property->account_id, $property->property_id)->getItems();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }
    }

    /**
     * getGoals
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    private function getGoals($property) {
        try {
            return $this->analytics->management_goals->listManagementGoals($property->account_id, $property->property_id, '~all')->getItems();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }
   }

    /**
     * getAccountIds
     * Returning the first account id.
     */
    private function getAccountIds() {
        /* Getting accounts */
        try {
            $accounts = $this->analytics->management_accounts->listManagementAccounts();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }
        $items = $accounts->getItems();
        if (count($items) <= 0) {
            return null;
        }

        /* Getting properties */
        $ids = array();
        foreach ($items as $account) {
            array_push($ids, $account->getId());
        }
        return $ids;
    }

} /* GoogleAnalyticsDataCollector */
