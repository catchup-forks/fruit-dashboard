<?php

class DataPopulator
{
    /**
     * The user object.
     *
     * @var User
     */
    protected $user = null;

    /**
     * The name of the service.
     *
     * @var string
     */
    protected $service = '';

    /**
     * The data objects.
     *
     * @var array
     */
    protected $dataObjects = array();

    /**
     * The data criteria.
     *
     * @var array
     */
    protected $criteria = null;

    /**
     * Main job handler.
     */
    public function fire($job, $data) {
        /* Init */
        Log::info("Starting data collection at " . Carbon::now()->toDateTimeString());
        $time = microtime(true);
        $this->user     = User::find($data['user_id']);
        $this->criteria = $data['criteria'];
        $this->service  = $data['service'];

        /* Getting data objects. */
        $this->dataObjects = $this->getDataObjects();

        /* Running data collection. */
        $this->populate();

        /* Running data collection. */
        $this->activate();

        /* Finish */
        Log::info("Data collection finished and it took " . (microtime(true) - $time) . " seconds to run.");

        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function populate() {
        foreach ($this->dataObjects as $data) {
            if ($data->decode() == false) {
                try {
                    $data->initialize();
                    $data->setState('active');
                } catch (ServiceException $e) {
                    Log::error($e->getMessage());
                    $data->setState('data_source_error');
                }
            }
        }
    }

    /**
     * Getting the page specific DataManagers
     * @return array
     */
    protected function getDataObjects() {
        $dataObjects = array();

        foreach ($this->user->dataObjects()->get() as $data) {
            if ($data->getDescriptor()->category == $this->service &&
                    $data->getCriteria() == $this->criteria) {
                $dataObjects[$data->getDescriptor()->type] = $data;
            }
        }

        return $dataObjects;
    }

    /**
     * activate
     * Setting all related widget's state to active.
     */
    protected function activate() {
        foreach ($this->dataObjects as $dataObject) {
            $dataObject->setState('active');
        }
    }

}
