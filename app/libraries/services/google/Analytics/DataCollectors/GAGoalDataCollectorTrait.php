<?php

trait GAGoalDataCollectorTrait
{
    /**
     * getGoal
     * --------------------------------------------------
     * Return the corresponding goal.
     * @return GoogleAnalyticsGoal
     * --------------------------------------------------
    */
    public function getGoal() {
        $profile = $this->getProfile(array('google_analytics_profiles.id'));
        if (is_null($profile)) {
            return null;
        }
        return $profile->goals()->where('goal_id', $this->getGoalId());
    }

    /**
     * getGoalId
     * --------------------------------------------------
     * Return the corresponding goal id.
     * @return int
     * --------------------------------------------------
    */
    public function getGoalId() {
        return $this->criteria['goal'];
    }

    /**
     * getCriteriaFields
     * Return the criteria fields for this collector.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public static final function getCriteriaFields()
    {
        return array_merge(parent::getCriteriaFields(), array('profile', 'goal'));
    }
}
?>
