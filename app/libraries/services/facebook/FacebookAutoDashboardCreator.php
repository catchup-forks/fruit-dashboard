<?php

class FacebookAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;
    /* -- Class properties -- */

    /* LATE STATIC BINDING. */
    protected static $positioning = array(
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


    /**
     * Populating the widgets with data.
     */
    protected function populateDashboard() {
        $likesWidget     = $this->widgets['facebook_likes'];
        $newLikesWidget  = $this->widgets['facebook_new_likes'];

        /* Creating data for the last DAYS days. */
        $dailyLikes = $this->collector->getInsight(
            'page_fans', $this->page,
            array(
                'since' => Carbon::now()->subDays(self::DAYS)->getTimestamp(),
                'until' => Carbon::now()->getTimestamp()
            )
        );

        $likesData    = array();
        $newLikesData = array();
        foreach ($dailyLikes[0]['values'] as $likes) {
            $date = Carbon::createFromTimestamp(strtotime($likes['end_time']))->toDateString();

            array_push($likesData, array(
                'date'  => $date,
                'value' => $likes['value']
            ));
            if ( ! isset($lastValue)) {
                $newLikes = 0;
            } else {
                $newLikes = $likes['value'] - $lastValue;
            }

            array_push($newLikesData, array(
                'date'  => $date,
                'value' => $newLikes
            ));

            $lastValue = $likes['value'];
        }

        $likesWidget->data->raw_value    = json_encode($likesData);
        $newLikesWidget->data->raw_value = json_encode($newLikesData);

        $likesWidget->data->save();
        $newLikesWidget->data->save();
    }

}