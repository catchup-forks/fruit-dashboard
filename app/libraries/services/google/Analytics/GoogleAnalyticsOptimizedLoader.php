<?php
/**
* --------------------------------------------------------------------------
* GoogleAnalyticsOptimizedLoader:
*       Merging multiple google analytics queries.
* --------------------------------------------------------------------------
*/
class GoogleAnalyticsOptimizedLoader
{
    /**
     * Array of GoogleAnalyticsQueries.
     *
     * @var array
     */
    private $queries = array();

    /**
     * The DataCollector object
     *
     * @var GoogleAnalyticsDataCollector
     */
    private $dataCollector = null;

    /**
     * Array of DataManagers.
     *
     * @var array
     */
    private $dataManagers = array();

    function __construct($user, $dataManagers) {
        /* Creating DataCollector instance */
        $this->dataCollector = new GoogleAnalyticsDataCollector($user);

        /* Getting the user's data managers. */
        $this->dataManagers = $dataManagers;

        /* Running optimizer. */
        $this->optimize();
    }

    /**
     * execute
     * Sending requests to the google api.
     * --------------------------------------------------
     * @return array
     * @param Carbon $start
     * @param Carbon $end
     * @param string $forceDimension
     * --------------------------------------------------
     */
    public function execute(Carbon $start, Carbon $end, $forceDimension='') {
        $data = array();
        foreach ($this->queries as $query) {

            /* If forcing the dimension overwriting the defaults */
            if ($forceDimension) {
                $optParams = array('dimensions' => $forceDimension);
            } else {
                $optParams = $query->getOptParams();
            }

            /* Sending request to the API. */
            $query->setData(
                $this->dataCollector->getMetrics(
                    $query->getProfileId(),
                    $start->toDateString(), $end->toDateString(),
                    $query->getMetrics(), $optParams
                )
            );

            /* Setting timestamp. */
            $query->setTimeStamp($end->getTimeStamp());

            /* Populating manager data. */
            $this->populateManagers($query);
        }
    }

    /**
     * populateManagers
     * Calling collectData on dataManagers with the correct
     * parameters.
     * --------------------------------------------------
     * @param GoogleAnalyticsQuery $query
     * --------------------------------------------------
     */
    private function populateManagers($query) {
        foreach ($query->getManagers() as $managerId) {

            /* Getting data and mangager. */
            $dataManager = DataManager::find($managerId);
            $data = $this->selectDataForManager(
                $dataManager, $query->getData()
            );

            if (empty($data)) {
                /* No data filtered. */
                continue;
            }

            if ($dataManager instanceof HistogramDataManager) {
                /* Histogram data manager. */
                $data = $dataManager->flatData($data);
                if (is_array(array_values($data)[0])) {
                    /* The data returned from the API is an array
                     * Probably because, a time range was queried. */
                    $dataManager->saveHistogram($data);
                } else {
                    $entry = $data;
                    $entry['timestamp'] = $query->getTimeStamp();
                    $dataManager->collectData(array(
                        'entry' => $entry,
                        'sum' => TRUE
                    ));
                }
            } else if ($dataManager instanceof TopSourcesDataManager) {

            }
        }
    }

    /**
     * selectDataForManager
     * Selecting the relevant data for the manager.
     * --------------------------------------------------
     * @param DataManager $dataManager
     * @param array $data
     * @return array
     * --------------------------------------------------
     */
    private function selectDataForManager(DataManager $dataManager, array $data) {
        $filteredData = array();
        foreach ($data as $key=>$value) {
            if (in_array($key, $dataManager->getMetricNames())) {
                array_push($filteredData, array($key=>$value));
            }
        }
        return $filteredData;
    }

    /**
     * optimize
     * Optimizing the queries based on optional parameters.
     */
    private function optimize() {
        $optimizedQueries = array();
        foreach ($this->dataManagers as $dm) {
            $hash = $this->getHash($dm);
            $match = FALSE;

            /* Trying to find a match. */
            foreach (array_keys($optimizedQueries) as $iHash) {
                if ($iHash == $hash) {
                    $optimizedQueries[$iHash]->addManager($dm);
                    $match = TRUE;
                }
            }

            if ( ! $match) {
                $optimizedQueries[$hash] = new GoogleAnalyticsQuery($dm);
            }
        }

        /* Override queries */
        $this->queries = $optimizedQueries;
    }

    /**
     * getHash
     * Creating a hash of a data manager
     * --------------------------------------------------
     * @param DataManager $dataManager
     * --------------------------------------------------
     */
    private function getHash(DataManager $dataManager) {
        return $dataManager->getCriteria()['profile'] . '_' . implode('|', $dataManager->getOptionalParams());
    }
}

class GoogleAnalyticsQuery
{
    /**
     * An array of metrics.
     *
     * @var array
     */
    private $metrics = array();

    /**
     * The GA profile id.
     *
     * @var string
     */
    private $profileId = '';

    /**
     * The optional parameters in the request.
     *
     * @var array
     */
    private $optParams = array();

    /**
     * The query timestamp
     *
     * @var int
     */
    private $timestamp = 0;

    /**
     * The data from the request.
     *
     * @var array
     */
    private $data = array();

    /**
     * The GA profile id.
     *
     * @var int
     */
    private $dataManagers = array();

    function __construct(DataManager $dataManager) {
        /* Assigning values. */
        $this->metrics   = $dataManager->getMetricNames();
        $this->optParams = $dataManager->getOptionalParams();
        $this->profileId = $dataManager->getCriteria()['profile'];
        array_push($this->dataManagers, $dataManager->id);
    }

    public function getProfileId() {
        return $this->profileId;
    }

    public function getMetrics() {
        return $this->metrics;
    }

    public function getOptParams() {
        return $this->optParams;
    }

    public function getManagers() {
        return $this->dataManagers;
    }

    public function getData() {
        return $this->data;
    }

    public function getTimeStamp() {
        return $this->timestamp;
    }

    public function setTimeStamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    public function setData($data) {
        $this->data = $data;
    }

    /**
     * addManager
     * Adding another manager into the array.
     * --------------------------------------------------
     * @param array $metrics
     * --------------------------------------------------
     */
    public function addManager(DataManager $dataManager) {
        $this->metrics = array_merge($this->metrics, $dataManager->getMetricNames());
        array_push($this->dataManagers, $dataManager->id);
    }

}
