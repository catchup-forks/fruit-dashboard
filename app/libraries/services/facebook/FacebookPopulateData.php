<?php

class FacebookPopulateData
{
    /**
     * The facebook collector object.
     *
     * @var FacebookDataCollector
     */
    private $collector = null;

    /**
     * The user object.
     *
     * @var User
     */
    private $user = null;

    /**
     * The Facebook Page.
     *
     * @var FacebookPage
     */
    private $page = null;

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
        $this->user = User::find($data['user_id']);
        $time = microtime(TRUE);
        Log::info("Starting Facebook data collection for user #". $this->user->id . " at " . Carbon::now()->toDateTimeString());
        $this->collector = new FacebookDataCollector($this->user);

        $this->criteria = $data['criteria'];
        $this->page = $this->user->facebookPages()->where('id', $data['criteria']['page'])->first();
        $this->dataManagers = $this->getManagers();
        $this->populateData();
        Log::info("Facebook data collection finished and it took " . (microtime(TRUE) - $time) . " seconds to run.");

        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        /* Getting metrics. */
        $likesData        = $this->getLikes();
        $impressionsData  = $this->getPageImpressions();
        $engagedUsersData = $this->getEngagedUsers();

        /* Saving values. */
        $this->dataManagers['facebook_likes']->saveData($likesData);
        $this->dataManagers['facebook_page_impressions']->saveData($impressionsData);
        $this->dataManagers['facebook_engaged_users']->saveData($engagedUsersData);

        foreach ($this->dataManagers as $manager) {
            $manager->setWidgetsState('active');
        }
    }

    /**
     * Getting the page specific DataManagers
     * @return array
     */
    private function getManagers() {
        $dataManagers = array();

        foreach ($this->user->dataManagers()->get() as $dataManager) {

            if ($dataManager->descriptor->category == 'facebook' && $dataManager->getCriteria() == $this->criteria) {
                $dataManagers[$dataManager->descriptor->type] = $dataManager;
            }
        }

        return $dataManagers;
    }

    /**
     * Getting the data for the likes widget.
     *
     * @return array
     */
    private function getLikes() {
        $dailyLikes = $this->collector->getPopulateHistogram($this->page->id, 'page_fans');
        $likesData = array();
        foreach ($dailyLikes[0]['values'] as $likes) {
            $date = Carbon::createFromTimestamp(strtotime($likes['end_time']));
            array_push($likesData, array(
                'value'     => $likes['value'],
                'timestamp' => $date->getTimestamp()
            ));
        }

        return $likesData;
    }

    /**
     * Getting the data for the engaged users widget.
     *
     * @return array
     */
    private function getEngagedUsers() {
        $dailyEngagedUsers = $this->collector->getPopulateHistogram($this->page->id, 'page_engaged_users');
        $engagedUsersData = array();
        foreach ($dailyEngagedUsers[0]['values'] as $engagedUsers) {
            $date = Carbon::createFromTimestamp(strtotime($engagedUsers['end_time']));
            array_push($engagedUsersData, array(
                'value'     => $engagedUsers['value'],
                'timestamp' => $date->getTimestamp()
            ));
        }

        return $engagedUsersData;
    }

    /**
     * Getting the page_impressions for the page_impressions widget.
     *
     * @return array
     */
    private function getPageImpressions() {
        $dailyImpressions = $this->collector->getPopulateHistogram($this->page->id, 'page_impressions_unique');
        $pageImpressionsData = array();
        foreach ($dailyImpressions[0]['values'] as $impressions) {
            $date = Carbon::createFromTimestamp(strtotime($impressions['end_time']));
            array_push($pageImpressionsData, array(
                'value'     => $impressions['value'],
                'timestamp' => $date->getTimestamp()
            ));
        }

        return $pageImpressionsData;
    }
}