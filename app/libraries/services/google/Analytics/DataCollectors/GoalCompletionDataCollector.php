<?php

class GoalCompletionDataCollector extends MultipleHistogramDataCollector
{
    use GAHistogramBySourceDataCollectorTrait, GAGoalDataCollectorTrait {
        GAGoalDataCollectorTrait::getCriteriaFields insteadof GAHistogramBySourceDataCollectorTrait;
    }

    public function getCurrentValue() {
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
