<?php

trait GoogleAnalyticsGoalDataManagerTrait
{
    /**
     * getGoal
     * --------------------------------------------------
     * Returning the corresponding goal.
     * @return GoogleAnalyticsGoal
     * --------------------------------------------------
    */
    public function getGoal() {
        $profile = $this->user->googleAnalyticsGoal()
            ->where('goal_id', $this->getGoalId())
            ->first();
        /* Invalid profile in DB. */
        return $profile;
    }

    /**
     * getGoalId
     * --------------------------------------------------
     * Returning the corresponding goal id.
     * @return int
     * --------------------------------------------------
    */
    public function getGoalId() {
        return $this->getCriteria()['goal'];
    }
}
?>
