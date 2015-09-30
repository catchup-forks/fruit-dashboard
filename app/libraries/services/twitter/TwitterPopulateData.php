<?php

class TwitterPopulateData
{
    /**
     * The user object.
     *
     * @var User
     */
    private $user = null;

    /**
     * The dataManagers.
     *
     * @var array
     */
    private $dataManagers = null;

    /**
     * Main job handler.
     */
    public function fire($job, $data) {
        $this->user = User::find($data['user_id']);
        $time = microtime(TRUE);
        Log::info("Starting Twitter data collection for user #". $this->user->id . " at " . Carbon::now()->toDateTimeString());
        $this->dataManagers = $this->getManagers();
        $this->populateData();
        Log::info("Twitter data collection finished and it took " . (microtime($time) - $time) . " seconds to run.");
        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        $this->dataManagers['twitter_followers']->initializeData();
        $this->dataManagers['twitter_mentions']->initializeData();
        foreach ($this->dataManagers as $manager) {
            $manager->setWidgetsState('active');
        }
    }

    /**
     * Getting the DataManagers
     * @return array
     */
    private function getManagers() {
        $dataManagers = array();

        foreach ($this->user->dataManagers()->get() as $generalDataManager) {
            $dataManager = $generalDataManager->getSpecific();
            if ($dataManager->descriptor->category == 'twitter') {
                /* Setting dataManager. */
                $dataManagers[$dataManager->descriptor->type] = $dataManager;
            }
        }

        return $dataManagers;
    }
}