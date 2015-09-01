<?php

class FacebookAutoDashboardCreator
{
    /* -- Class properties -- */
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
     * All calculated widgets.
     *
     * @var array
     */
    private $widgets = array();

    /** * Main function of the job.
     *
     * @param $job
     * @param array $data
     * @throws FacebookNotConnected
    */
    public function fire($job, $data) {
        /* Getting the user */
        if ( ! isset($data['user_id'])) {
            return;
        }
        $this->user = User::find($data['user_id']);
        $this->page = FacebookPage::find($data['page_id']);

        if (is_null($this->user) || is_null($this->page)) {
            /* User or page not found */
            return;
        }

        /* Creating dashboard. */
        $this->createDashboard();

        /* Change trial period settings */
        $this->user->subscription->changeTrialState('active');

        /* Creating calculator. */
        $this->collector = new FacebookDataCollector($this->user);

        /* Populate dashboard. */
        $this->populateDashboard();

    }

    /**
     * ================================================== *
     *                  PRIVATE SECTION                   *
     * ================================================== *
    */

    /**
     * Creating a dashboard dedicated to facebook widgets. */
    private function createDashboard() {
        /* Creating dashboard. */
        $dashboard = new Dashboard(array(
            'name'       => 'Facebook dashboard',
            'background' => TRUE,
            'number'     => $this->user->dashboards->max('number') + 1
        ));
        $dashboard->user()->associate($this->user);
        $dashboard->save();

        /* Adding widgets */
        $likesWidget = new FacebookLikesWidget(array(
            'position' => '{"col":2,"row":7,"size_x":5,"size_y":5}',
            'state'    => 'loading',
        ));

        $newLikesWidget = new FacebookNewLikesWidget(array(
            'position' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
            'state'    => 'loading',
        ));

        /* Associating dashboard */
        $likesWidget->dashboard()->associate($dashboard);
        $newLikesWidget->dashboard()->associate($dashboard);

        /* Saving widgets */
        $likesWidget->save();
        $newLikesWidget->save();

        $this->widgets = array(
            'likes'    => $likesWidget,
            'new_likes' => $newLikesWidget,
        );
    }

    /**
     * Populating the widgets with data.
     */
    private function populateDashboard() {
        $likesWidget     = $this->widgets['likes'];
        $newLikesWidget  = $this->widgets['new_likes'];

        /* Creating data for the last DAYS days. */
        $dailyLikes = $this->collector->getInsight('page_fans', $this->page);

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

        $likesWidget->setSetting('page', $this->page->id, FALSE);
        $newLikesWidget->setSetting('page', $this->page->id, FALSE);

        $likesWidget->data->save();
        $newLikesWidget->data->save();

        $likesWidget->state    = 'active';
        $newLikesWidget->state = 'active';

        /* Saving widgets */
        $likesWidget->save();
        $newLikesWidget->save();
    }

}