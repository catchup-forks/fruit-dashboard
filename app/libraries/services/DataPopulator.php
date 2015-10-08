<?php

class DataPopulator
{
    /**
     * The user object.
     *
     * @var User
     */
    private $user = null;

    /**
     * The name of the service.
     *
     * @var string
     */
    private $service = '';

    /**
     * The dataManagers.
     *
     * @var array
     */
    private $dataManagers = null;

    /**
     * The dataManager criteria.
     *
     * @var array
     */
    private $criteria = null;

    /**
     * Main job handler.
     */
    public function fire($job, $data) {
        /* Init */
        Log::info("Starting data collection at " . Carbon::now()->toDateTimeString());
        $time = microtime(TRUE);
        $this->user     = User::find($data['user_id']);
        $this->criteria = $data['criteria'];
        $this->service  = $data['service'];

        /* Getting managers. */
        $this->dataManagers = $this->getManagers();

        /* Running data collection. */
        $this->populateData();

        /* Finish */
        Log::info("Data collection finished and it took " . (microtime(TRUE) - $time) . " seconds to run.");
        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    private function populateData() {
        foreach ($this->dataManagers as $manager) {
            if ($manager->getData() == FALSE) {
                $manager->initializeData();
                $manager->setWidgetsState('active');
            }
        }
    }

    /**
     * Getting the page specific DataManagers
     * @return array
     */
    private function getManagers() {
        $dataManagers = array();

        foreach ($this->user->dataManagers()->get() as $generalDataManager) {
            $dataManager = $generalDataManager->getSpecific();

            if ($dataManager->descriptor->category == $this->service && $dataManager->getCriteria() == $this->criteria) {
                $dataManagers[$dataManager->descriptor->type] = $dataManager;
            }
        }

        return $dataManagers;
    }

}