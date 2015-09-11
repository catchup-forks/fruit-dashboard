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
        $this->dataManagers = $this->getManagers();
        $this->collectData();
        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function collectData() {
        $followersDataManager       = $this->dataManagers['twitter_followers'];
        $newFollowersDataManager    = $this->dataManagers['twitter_new_followers'];

        /* Getting metrics. */
        $followersDataManager->collectData();
        $newFollowersDataManager->collectData();
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