<?php

class BraintreeAutoDashboardCreator
{
    /* -- Class properties -- */
    const DAYS = 30;

    /**
     * The braintree subscriptions.
     *
     * @var array
     */
    private $subscriptions = null;

    /**
     * The braintree calculator object.
     *
     * @var BraintreeCalculator.
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
     * @param $data, array containing user_id. * @throws BraintreeNotConnected
     * --------------------------------------------------
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
        $this->calculator = new BraintreeCalculator($this->user);
        $this->subscriptions = $this->calculator->getCollector()->getAllSubscriptions();
        $this->filterSubscriptions();

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
     * Creating a dashboard dedicated to braintree widgets.
     * --------------------------------------------------
     */
    private function createDashboard() {
        /* Creating dashboard. */
        $dashboard = new Dashboard(array(
            'name'       => 'Braintree dashboard',
            'background' => TRUE,
            'number'     => $this->user->dashboards->max('number') + 1
        ));
        $dashboard->user()->associate($this->user);
        $dashboard->save();

        /* Adding widgets */ $mrrWidget = new BraintreeMrrWidget(array(
            'position' => '{"col":2,"row":1,"size_x":10,"size_y":6}',
            'state'    => 'loading',
        ));

        $arrWidget = new BraintreeArrWidget(array(
            'position' => '{"col":1,"row":7,"size_x":6,"size_y":4}',
            'state'    => 'loading',
        ));

        $arpuWidget = new BraintreeArpuWidget(array(
            'position' => '{"col":7,"row":7,"size_x":6,"size_y":4}',
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
     * filterSubscriptions
     * --------------------------------------------------
     * Filtering subscriptions to relevant only.
     * --------------------------------------------------
    */
    private function filterSubscriptions() {
        $filteredSubscriptions = array();
        foreach ($this->subscriptions as $key=>$subscription) {
            $updatedAt = Carbon::createFromTimestamp($subscription->updatedAt->getTimestamp());
            if ($updatedAt->between(Carbon::now(), Carbon::now()->subDays(30))) {
                array_push($filteredSubscriptions, $subscription);
            }
        }
        $this->subscriptions = $filteredSubscriptions;
    }

    /**
     * mirrorDay
     * --------------------------------------------------
     * Trying to mirror the specific date, to our DB.
     * @param date The date on which we're mirroring.
     * --------------------------------------------------
    */
    private function mirrorDay($date) {
        foreach ($this->subscriptions as $key=>$subscription) {
            foreach ($subscription->statusHistory as $statusDetail) {
                $updateDate = Carbon::createFromTimestamp($statusDetail->timestamp->getTimestamp())->toDateString();
                if ($updateDate == $date) {
                    switch ($statusDetail->status) {
                        case Braintree_Subscription::CANCELED: $this->handleSubscriptionDeletion($subscription); break;
                        case Braintree_Subscription::ACTIVE: $this->handleSubscriptionCreation($subscription); break;
                        default:;
                    }
                }
            }
        }
    }

    /**
     * handleSubscriptionDeletion
     * --------------------------------------------------
     * Handling subscription deletion.
     * On deletion we'll have to create a subscription.
     * @param subscription The braintree subscription.
     * --------------------------------------------------
    */
    private function handleSubscriptionDeletion($subscription) {
        $newSubscription = new BraintreeSubscription(array(
            'subscription_id' => $subscription->id,
            'start'           => $subscription->firstBillingDate,
            'status'          => Braintree_Subscription::ACTIVE,
            'customer_id'     => $subscription->transactions[0]->customer['id']
        ));

        // Creating the plan if necessary.
        $plan = BraintreePlan::where('plan_id', $subscription->planId)->first();
        if (is_null($plan)) {
            return;
        }
        $newSubscription->plan()->associate($plan);
        $newSubscription->save();
    }

    /**
     * handleSubscriptionCreation
     * --------------------------------------------------
     * Handling subscription creation.
     * On creation we'll have to delete a subscription.
     * @param subscription The braintree subscription
     * --------------------------------------------------
    */
    private function handleSubscriptionCreation($subscription) {
        BraintreeSubscription::where('subscription_id', $subscription->id)->first()->delete();
    }
}