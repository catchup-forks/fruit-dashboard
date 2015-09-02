<?php

class TwitterAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */

    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'twitter_followers'     => '{"col":2,"row":7,"size_x":5,"size_y":5}',
        'twitter_new_followers' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'twitter';
    /* /LATE STATIC BINDING. */

    /**
     * The facebook collector object.
     *
     * @var FacebookDataCollector
     */
    private $collector = null;

    /**
     * Setting up the collector.
     */
    protected function setup($args) {
        $this->collector = new TwitterDataCollector($this->user);
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        $followersDataManager       = $this->dataManagers['twitter_followers'];
        $newFollowersDataManager    = $this->dataManagers['twitter_new_followers'];

        /* Getting metrics. */
        $followersDataManager->collectData();
        $newFollowersDataManager->collectData();
    }

}