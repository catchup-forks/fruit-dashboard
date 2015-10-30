<?php

trait GADataCollectorTrait
{
    /**
     * getCollector
     * Return a data collector
     * --------------------------------------------------
     * @return FacebookDataCollector
     * --------------------------------------------------
     */
    protected function getCollector()
    {
        $collector = new GoogleAnalyticsDataCollector($this->user);

        return $collector;
    }

    /**
     * getMetricNames
     * Return the names of the metric used by the DM.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getMetricNames()
    {
        return static::$metrics;
    }

    /**
     * getCriteriaFields
     * Return the criteria fields for this collector.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public static function getCriteriaFields()
    {
        return array_merge(parent::getCriteriaFields(), array('profile'));
    }

    /**
     * getOptionalParams
     * Return the optional parameters used by the DM.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getOptionalParams()
    {
        return array();
    }

    /**
     * getProperty
     * --------------------------------------------------
     * Return the corresponding property.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getProperty()
    {
        $profile = $this->getProfile(array('property_id'));
        /* Invalid profile in DB. */
        if (is_null($profile)) {
            return null;
        }

        $property = $this->user->googleAnalyticsProperties()
            ->where('property_id', $profile->property_id)->first();

        return $property;
    }

    /**
     * getProfile
     * --------------------------------------------------
     * Return the corresponding profile.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getProfile(array $attributes=array('*'))
    {
        $profile = $this->user->googleAnalyticsProfiles()
            ->where('profile_id', $this->getProfileId())
            ->first($attributes);
        /* Invalid profile in DB. */
        return $profile;
    }

    /**
     * getProfileId
     * --------------------------------------------------
     * Return the corresponding profile id.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getProfileId()
    {
        return $this->criteria['profile'];
    }
}
?>
