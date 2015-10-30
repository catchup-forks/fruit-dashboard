<?php

class GoogleAnalyticsGoalCompletionDataManager extends MultipleHistogramDataManager
{
    use GoogleAnalyticsGoalDataManagerTrait;
    use GoogleAnalyticsHistogramBySourceDataManagerTrait;

    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        return $this->getCollector()->getGoalCompletions($this->getProfileId(), $this->getGoalId());
    }

    /**
     * getMetricNames
     * Return the names of the metric used by the DM.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getMetricNames() {
        return array('goal' . $this->criteria['goal'] . 'Completions');
    }
}
?>
