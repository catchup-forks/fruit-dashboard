<?php

class FacebookAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;
    /* -- Class properties -- */

    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'facebook_page_impressions'  => '{"col":4,"row":1,"size_x":6,"size_y":6}',
        'facebook_likes'     => '{"col":2,"row":7,"size_x":5,"size_y":5}',
        'facebook_new_likes' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'facebook';
    /* /LATE STATIC BINDING. */

    /**
     * The facebook collector object.
     *
     * @var FacebookDataCollector
     */
    private $collector = null;

    /**
     * The facebook page.
     *
     * @var FacebookPage
     */
    private $page = null;

    /**
     * Setting up the collector.
     */
    protected function setup($args) {
        $this->collector = new FacebookDataCollector($this->user);
        $this->page = FacebookPage::find($args['page_id']);
    }

    protected function createWidgets() {
        parent::createWidgets();
        foreach ($this->widgets as $widget) {
            $widget->setSetting('page', $this->page->id);
        }
    }
    protected function createManagers() {
        parent::createManagers();
        foreach ($this->dataManagers as $dataManager) {
            $dataManager->settings_criteria = json_encode(array(
                'page' => $this->page->id
            ));
            $dataManager->save();
        }
    }


    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        $likesDataManager       = $this->dataManagers['facebook_likes'];
        $newLikesDataManager    = $this->dataManagers['facebook_new_likes'];
        $impressionsDataManager = $this->dataManagers['facebook_page_impressions'];

        /* Getting metrics. */
        $likesData       = $this->getLikes();
        $impressionsData = $this->getPageImpressions();

        /* Saving values. */
        $likesDataManager->data->raw_value       = json_encode($likesData);
        $newLikesDataManager->data->raw_value    = json_encode($this->getDiff($likesData));
        $impressionsDataManager->data->raw_value = json_encode($impressionsData);
    }

    /**
     * Getting the last DAYS entries for a specific insight
     *
     * @param string $insight
     * @return array
     */
    private function getHistogram($insight) {
        return $this->collector->getInsight(
            $insight, $this->page,
            array(
                'since' => Carbon::now()->subDays(self::DAYS)->getTimestamp(),
                'until' => Carbon::now()->getTimestamp()
            )
        );
    }

    /**
     * Getting the data for the likes widget.
     *
     * @return array
     */
    private function getLikes() {
        $dailyLikes = $this->getHistogram('page_fans');
        $likesData = array();
        foreach ($dailyLikes[0]['values'] as $likes) {
            $date = Carbon::createFromTimestamp(strtotime($likes['end_time']))->toDateString();
            array_push($likesData, array(
                'date'  => $date,
                'value' => $likes['value']
            ));
        }

        return $likesData;
    }

    /**
     * Getting the page_impressions for the page_impressions widget.
     *
     * @return array
     */
    private function getPageImpressions() {
        $dailyImpressions = $this->getHistogram('page_impressions_unique');
        $pageImpressionsData = array();
        foreach ($dailyImpressions[0]['values'] as $impressions) {
            $date = Carbon::createFromTimestamp(strtotime($impressions['end_time']))->toDateString();
            array_push($pageImpressionsData, array(
                'date'  => $date,
                'value' => $impressions['value']
            ));
        }

        return $pageImpressionsData;
    }
}