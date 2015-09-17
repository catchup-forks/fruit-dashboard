<?php

class GoogleAnalyticsTopSourcesDataManager extends TableDataManager
{
    use GoogleAnalyticsDataManagerTrait;

    private static $metrics = array('sessions', 'users');

    public function updateTable() {
        $metricsData = array();

        $collector = new GoogleAnalyticsDataCollector($this->user);
        foreach ($collector->getMetrics($this->getProperty(), '4daysAgo', 'today', self::$metrics, array('dimensions' => 'ga:source', 'sort' => '-ga:sessions', 'max-results' => '5')) as $metric=>$data)  {
            foreach ($data as $source=>$value) {
                $metricsData[$source][ucwords($metric)] = $value[0];
            }
        }

        $this->initTable();

        foreach ($metricsData as $metric=>$row) {
            $this->insert(array_merge(array('Metric' => $metric), $row));
        }
    }

    private function initTable() {
        foreach ($this->getContent() as $i=>$row) {
            $this->deleteRow($i);
        }
        $this->addCol('Metric');
        foreach (self::$metrics as $metric) {
            $this->addCol(ucwords($metric));
        }
    }
}