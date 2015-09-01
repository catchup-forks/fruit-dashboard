<?php

/**
* --------------------------------------------------------------------------
* GoogleAnalyticsDataCollector:
*       Getting data from google account.
* --------------------------------------------------------------------------
*/

class GoogleAnalyticsDataCollector
{
    /* -- Class properties -- */
    private $user;
    private $client;
    private $analytics;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new GoogleAnalyticsConnector($user);
        $connector->connect();
        $this->client = $connector->getClient();
        $this->analytics = new Google_Service_Analytics($this->client);
    }

    /**
     * saveProfiles
     * Saves a user's google analytics profiles.
     * --------------------------------------------------
     * --------------------------------------------------
     */
    public function saveProfiles() {
        /* TODO */
    }

    /**
     * getFirstProfileId
     * Returns the first GA profile.
     * --------------------------------------------------
     * --------------------------------------------------
     */
    public function getFirstProfileId() {
        /* Getting accounts */
        $accounts = $this->analytics->management_accounts->listManagementAccounts();
        $items = $accounts->getItems();
        if (count($items) <= 0) {
            return null;
        }

        /* Getting properties */
        $firstAccountId = $items[0]->getId();
        $properties = $this->analytics->management_webproperties->listManagementWebproperties($firstAccountId);

        $items = $properties->getItems();
        if (count($items) <= 0) {
            return null;
        }

        /* Getting profiles */
        $firstPropertyId = $items[0]->getId();
        $profiles = $this->analytics->management_profiles->listManagementProfiles($firstAccountId, $firstPropertyId);

        $items = $profiles->getItems();
        if (count($items) <= 0) {
            return null;
        }

        return $items[0]->getId();
    }


} /* GoogleAnalyticsDataCollector */
