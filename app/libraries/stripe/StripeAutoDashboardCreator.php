<?php

class StripeAutoDashboardCreator
{
    /* -- Class properties -- */
    public static $allowedEventTypes = array(
        'customer.subscription.created',
        'customer.subscription.updated',
        'customer.subscription.deleted'
    );

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

    /**
     * fire
     * --------------------------------------------------
     * Main function of the job.
     * @param $job, the job instance.
     * @param $data, array containing user_id.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function fire($job, $data) {
        /* Getting the user */
        if (!isset($data['user_id'])) {
            return;
        }
        $this->user = User::find($data['user_id']);
        if (is_null($this->user)) {
            /* User not found */
            return;
        }

        /* Creating dashboard. */
        $this->createDashboard();

        /* Creating calculator. */
        $this->calculator = new StripeCalculator($this->user);
        $this->events = $this->calculator->getCollector()->getEvents();

        /* Populate dashboard. */
        $this->populateDashboard();

    }

    /**
     * ================================================== *
     *                  PRIVATE SECTION                   *
     * ================================================== *
    */

    /**
     * createDashboard
     * --------------------------------------------------
     * Creating a dashboard dedicated to stripe widgets.
     * --------------------------------------------------
     */
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
            'position'  => '{"col":1,"row":7,"size_x":4,"size_y":4}',
            'state'     => 'loading',
        ));

        $arrWidget = new StripeArrWidget(array(
            'position'  => '{"col":5,"row":7,"size_x":4,"size_y":4}',
            'state'     => 'loading',
        ));

        $arpuWidget = new StripeArpuWidget(array(
            'position'  => '{"col":9,"row":7,"size_x":4,"size_y":4}',
            'state'     => 'loading',
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
     * populateDashboard
     * --------------------------------------------------
     * Populating the widgets with data.
     * --------------------------------------------------
     */
    private function populateDashboard() {
        $mrrWidget  = $this->widgets['mrr'];
        $arrWidget  = $this->widgets['arr'];
        $arpuWidget = $this->widgets['arpu'];

        /* Creating data for the last 30 days. */
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
     * getLastMonthData
     * --------------------------------------------------
     * Returning all metrics in an array.
     * @return All metrics in an array.
     * --------------------------------------------------
    */
    private function getMetrics() {
        /* Updating subscriptions to be up to date. */
        $this->calculator->getCollector()->updateSubscriptions();

        $mrr = array();
        $arr = array();
        $arpu = array();

        for ($i = 0; $i < 30; $i++) {
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
     * sortByDate
     * --------------------------------------------------
     * Sorting a multidimensional dataset by date.
     * @param dataSet The data to be sorted.
     * @return array the sorted dataset.
     * --------------------------------------------------
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
     * filterEvents
     * --------------------------------------------------
     * Filtering events to relevant only.
     * --------------------------------------------------
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
     * mirrorDay
     * --------------------------------------------------
     * Trying to mirror the specific date, to our DB.
     * @param date The date on which we're mirroring.
     * --------------------------------------------------
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
     * handleSubscriptionDeletion
     * --------------------------------------------------
     * Handling subscription deletion.
     * On deletion we'll have to create a subscription.
     * @param eventThe specific stripe event.
     * --------------------------------------------------
    */
    private function handleSubscriptionDeletion($event) {
        $subscriptionData = $event['data']['object'];
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
        $subscription->push();
    }

    /**
     * handleSubscriptionUpdate
     * --------------------------------------------------
     * Handling subscription update.
     * @param eventThe specific stripe event.
     * --------------------------------------------------
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
     * handleSubscriptionCreation
     * --------------------------------------------------
     * Handling subscription creation.
     * On creation we'll have to delete a subscription.
     * @param eventThe specific stripe event.
     * --------------------------------------------------
    */
    private function handleSubscriptionCreation($event) {
        $subscriptionData = $event['data']['object'];
        StripeSubscription::where('subscription_id', $subscriptionData['id'])->first()->delete();
    }
}