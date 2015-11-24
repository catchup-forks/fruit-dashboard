<?php

class GoogleAnalyticsTopSourcesWidget extends TableWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('new_users', 'sessions');

    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

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
        $this->addCol('Sessions');
        $this->addCol('Ratio');
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
        $this->setActiveHistogram($this->data['new_users']);

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
        $this->setActiveHistogram($this->data['sessions']);
        $histogram = $this->buildHistogram();
        $sessions = end($histogram)[$source];

        /* New users. */
        $this->setActiveHistogram($this->data['new_users']);
        $histogram = $this->buildHistogram();
        $newUsers = end($histogram)[$source];
        
        /* Conversions. */
        $ratio = $sessions / $newUsers;

        /* Return the row. */
        return array(
            $this->getDatasetName($source),
            $sessions,
            $newUsers,
            sprintf('%.2f%%', $ratio * 100)
        );

    }
}
?>
