<?php

trait GoogleAnalyticsHistogramDataManagerTrait
{
    use GoogleAnalyticsDataManagerTrait;
    /**
     * initializeData
     * Creating, and saving data.
     */
    public function initializeData() {
        try {
            $data = array();
            for ($i = SiteConstants::getServicePopulationPeriod()['google_analytics']; $i >= 0; --$i) {
                /* Creating start, end days. */
                $start = SiteConstants::getGoogleAnalyticsLaunchDate();
                $end = Carbon::now()->subDays($i);
                $metrics = $this->getCollector()->getMetrics($this->getProperty(), $this->getCriteria()['profile'], $start, $end->toDateString(), array(static::$metric));
                array_push($data, array(
                    'timestamp' => $end->getTimestamp(),
                    'value'     => $metrics[static::$metric]
                ));
            }
            $this->saveData($data);
        } catch (ServiceException $e) {
            Log::error('Google connection error. ' . $e->getMessage());
            $this->delete();
        }
    }

    /**
     * getCollector
     * Returning a data collector
     * --------------------------------------------------
     * @return FacebookDataCollector
     * --------------------------------------------------
     */
    protected function getCollector() {
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector;
    }
}
?>
