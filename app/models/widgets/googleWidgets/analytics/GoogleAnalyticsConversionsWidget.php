<?php

/**  
 * A table of the number of conversions.
*/
class GoogleAnalyticsConversionsWidget extends TableWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('new_users', 'goal_completion');

    /* Service settings. */
    use GoogleAnalyticsGoalWidgetTrait;

    /* Histogram data transformation. */
    use MultipleHistogramWidgetTrait;

    /**
     * buildHeader
     * Building the table header.
     * --------------------------------------------------
     * @return null 
     * --------------------------------------------------
     */
    protected function buildHeader()
    {
        $this->addCol('Source');
        $this->addCol('Unique visitors');
        $this->addCol('Goal completion');
        $this->addCol('Conversions');
    }

    /**
     * buildContent
     * Building the table content.
     * --------------------------------------------------
     * @return null 
     * --------------------------------------------------
     */
    protected function buildContent()
    {
        $this->setActiveHistogram($this->data['goal_completion']);

        foreach ($this->filterDatasets() as $source) {
            $this->insert($this->getSourceData($source));
        }
    }

    /**
     * getSourceData
     * Adding the source data.
     * --------------------------------------------------
     * @param source
     * @return array 
     * --------------------------------------------------
     */
    private function getSourceData($source)
    {
        /* Initializing the row. */
        $row = array($this->getDatasetName($source));
        
        /* Goal completion users. */
        $this->setActiveHistogram($this->data['goal_completion']);
        $histogram = $this->buildHistogram();
        $goalCompletion = end($histogram)[$source];

        /* New users. */
        $this->setActiveHistogram($this->data['new_users']);
        $histogram = $this->buildHistogram();
        $newUsers = end($histogram)[$source];
        
        /* Conversions. */
        if ($newUsers == 0) {
            /* Escaping division by zero. */
            $conversions = 0; 
        } else {
            $conversions = $goalCompletion / $newUsers;
        }

        /* Return the row. */
        return array(
            $this->getDatasetName($source),
            $newUsers,
            $goalCompletion,
            sprintf('%.2f%%', $conversions * 100)
        );

    }
}
?>
