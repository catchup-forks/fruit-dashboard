<?php

class StripeAutoDashboardCreator
{
    /* -- Class properties -- */
    public static $allowedEventTypes = array(
        'customer.subscription.created',
        'customer.subscription.updated',
        'customer.subscription.deleted'
    );
    const DAYS = 30;

    /**
     * The stripe events.
     *
     * @var array
     */
    private $events = null;

    /**
     * The stripe calculator object.
     *
     * @var StripeCalculator
     */
    private $calculator = null;

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
     * @throws StripeNotConnected
    */
    public function fire($job, $data) {
        /* Getting the user */
        if ( ! isset($data['user_id'])) {
            return;
        }
        $this->user = User::find($data['user_id']);

        if (is_null($this->user)) {
            /* User not found */
            return;
        }

        /* Creating dashboard. */
        $this->createDashboard();

        /* Change trial period settings */
        $this->user->subscription->changeTrialState('active');

        /* Creating calculator. */
        $this->calculator = new StripeCalculator($this->user);
        $this->events = $this->calculator->getCollector()->getEvents();
        $this->filterEvents();

        /* Populate dashboard. */
        $this->populateDashboard();
    }

    /**
     * ================================================== *
     *                  PRIVATE SECTION                   *
     * ================================================== *
    */

    /**
     * Creating a dashboard dedicated to stripe widgets. */
    private function createDashboard() {
        /* Creating dashboard. */
        $dashboard = new Dashboard(array(
            'name'       => 'Stripe dashboard',
            'background' => TRUE,
            'number'     => $this->user->dashboards->max('number') + 1
        ));
        $dashboard->user()->associate($this->user);
        $dashboard->save();

        /* Adding widgets */
        $mrrWidget = new StripeMrrWidget(array(
            'position' => '{"col":4,"row":1,"size_x":6,"size_y":6}',
            'state'    => 'loading',
        ));

        $arrWidget = new StripeArrWidget(array(
            'position' => '{"col":2,"row":7,"size_x":5,"size_y":5}',
            'state'    => 'loading',
        ));

        $arpuWidget = new StripeArpuWidget(array(
            'position' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
            'state'    => 'loading',
        ));

        /* Associating dashboard */
        $mrrWidget->dashboard()->associate($dashboard);
        $arrWidget->dashboard()->associate($dashboard);
        $arpuWidget->dashboard()->associate($dashboard);

        /* Saving widgets */
        $mrrWidget->save();
        $arrWidget->save();
        $arpuWidget->save();

        $this->widgets = array(
            'mrr'  => $mrrWidget,
            'arr'  => $arrWidget,
            'arpu' => $arpuWidget,
        );
    }

    /**
     * Populating the widgets with data.
     */
    private function populateDashboard() {
        $mrrWidget  = $this->widgets['mrr'];
        $arrWidget  = $this->widgets['arr'];
        $arpuWidget = $this->widgets['arpu'];

        /* Creating data for the last DAYS days. */
        $metrics = $this->getMetrics();

        $mrrWidget->data->raw_value = json_encode($metrics['mrr']);
        $arrWidget->data->raw_value = json_encode($metrics['arr']);
        $arpuWidget->data->raw_value = json_encode($metrics['arpu']);

        $mrrWidget->data->save();
        $arrWidget->data->save();
        $arpuWidget->data->save();

        $mrrWidget->state = 'active';
        $arrWidget->state = 'active';
        $arpuWidget->state = 'active';

        /* Saving widgets */
        $mrrWidget->save();
        $arrWidget->save();
        $arpuWidget->save();
    }

    /**
     * Returning all metrics in an array.
     *
     * @return array.
    */
    private function getMetrics() {
        /* Updating subscriptions to be up to date. */
        $this->calculator->getCollector()->updateSubscriptions();

        $mrr = array();
        $arr = array();
        $arpu = array();

        for ($i = 0; $i < self::DAYS; $i++) {
            /* Calculating the date to mirror. */
            $date = Carbon::now()->subDays($i)->toDateString();
            $this->mirrorDay($date);
            array_push($mrr, array('date' => $date, 'value' => $this->calculator->getMrr()));
            array_push($arr, array('date' => $date, 'value' => $this->calculator->getArr()));
            array_push($arpu, array('date' => $date, 'value' => $this->calculator->getArpu()));
        }

        /* Sorting arrays accordingly. */
        return array(
            'mrr' => $this->sortByDate($mrr),
            'arr' => $this->sortByDate($arr),
            'arpu' => $this->sortByDate($arpu),
        );
    }



    /**
     * Sorting a multidimensional dataset by date.
     *
     * @param array dataSet
     * @return array
    */
    private function sortByDate($dataSet) {
        $dates = array();
        foreach($dataSet as $key=>$data) {
            $dates[$key] = $data['date'];
        }
        array_multisort($dates, SORT_ASC, $dataSet);
        return $dataSet;

    }

    /**
     * Filtering events to relevant only.
    */
    private function filterEvents() {
        $filteredEvents = array();
        foreach ($this->events as $key=>$event) {
            $save = FALSE;
            foreach (Static::$allowedEventTypes as $type) {
                if ($save) {
                    /* Save already set, going on. */
                    break;
                }
                if ($event['type'] == $type) {
                    $save = TRUE;
                }
            }
            if ($save) {
                array_push($filteredEvents, $event);
            }
        }
        $this->events = $filteredEvents;
    }

    /**
     * Trying to mirror the specific date, to our DB.
     *
     * @param date
    */
    private function mirrorDay($date) {
        foreach ($this->events as $key=>$event) {
            $eventDate = Carbon::createFromTimestamp($event['created'])->toDateString();
            if ($eventDate == $date) {
                switch ($event['type']) {
                    case 'customer.subscription.created': $this->handleSubscriptionCreation($event); break;
                    case 'customer.subscription.updated': $this->handleSubscriptionUpdate($event); break;
                    case 'customer.subscription.deleted': $this->handleSubscriptionDeletion($event); break; default:;
                }
                /* Making sure we're done with the event. */
                unset($this->events[$key]);
            }
        }
    }

    /**
     * Handling subscription deletion.
     *
     * @param Stripe\Event $event
    */
    private function handleSubscriptionDeletion($event) {
        $subscriptionData = $event['data']['object'];
        /* Creating a new susbcription */
        $subscription = new StripeSubscription(array(
            'subscription_id' => $subscriptionData['id'],
            'start'           => $subscriptionData['start'],
            'status'          => $subscriptionData['status'],
            'customer'        => $subscriptionData['customer'],
            'ended_at'        => $subscriptionData['ended_at'],
            'canceled_at'     => $subscriptionData['canceled_at'],
            'quantity'        => $subscriptionData['quantity'],
            'discount'        => $subscriptionData['discount'],
            'trial_start'     => $subscriptionData['trial_start'],
            'trial_end'       => $subscriptionData['trial_start'],
            'discount'        => $subscriptionData['discount']
        ));

        // Creating the plan if necessary.
        $plan = StripePlan::where('plan_id', $subscriptionData['plan']['id'])->first();
        if (is_null($plan)) {
            return;
        }

        $subscription->plan()->associate($plan);
        $subscription->save();
    }

    /**
     * Handling subscription update.
     *
     * @param Stripe\Event $event
    */
    private function handleSubscriptionUpdate($event) {
        /* Check if a plan's been changed./ */
        if (isset($event['data']['previous_attributes']['plan'])) {
            $subscriptionData = $event['data']['object'];

            $subscription = StripeSubscription::where('subscription_id', $subscriptionData['id'])->first();

            $newPlan = StripePlan::where('user_id', $this->user->id)->where('plan_id', $subscriptionData['plan']['id'])->first();

            $subscription->plan()->associate($newPlan);
            $subscription->save();
        }
    }

    /**
     * Handling subscription creation.
     *
     * @param Stripe\Event $event
    */
    private function handleSubscriptionCreation($event) {
        $subscriptionData = $event['data']['object'];
        /* Deleting the subscription */
        StripeSubscription::where('subscription_id', $subscriptionData['id'])->first()->delete();
    }
}