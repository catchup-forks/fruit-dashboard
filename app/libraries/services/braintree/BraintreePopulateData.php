<?php

class BraintreePopulateData
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
        Log::info("Starting Braintree data collection for user #". $this->user->id . " at " . Carbon::now()->toDateTimeString());
        $this->calculator = new BraintreeCalculator($this->user);
        $this->subscriptions = $this->calculator->getCollector()->getAllSubscriptions();
        $this->filterSubscriptions();
        $this->dataManagers = $this->getManagers();
        $this->populateData();
        Log::info("Braintree data collection finished and it took " . (microtime($time) - $time) . " seconds to run.");
        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        /* Creating data for the last 30 days. */
        $metrics = $this->getMetrics();

        $this->dataManagers['braintree_mrr']->saveData($metrics['mrr']);
        $this->dataManagers['braintree_arr']->saveData($metrics['arr']);
        $this->dataManagers['braintree_arpu']->saveData($metrics['arpu']);

        foreach ($this->dataManagers as $manager) {
            $manager->setState('active');
        }
    }

    /**
     * Getting the DataManagers
     * @return array
     */
    private function getManagers() {
        $dataManagers = array();

        foreach ($this->user->dataManagers()->get() as $dataManager) {
            if ($dataManager->getDescriptor()->category == 'braintree') {
                /* Setting dataManager. */
                $dataManagers[$dataManager->getDescriptor()->type] = $dataManager;
            }
        }

        return $dataManagers;
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
            $date = Carbon::now()->subDays($i);
            $this->mirrorDay($date->toDateString());
            array_push($mrr, array(
                'value'     => $this->calculator->getMrr(),
                'timestamp' => $date->getTimestamp()
            ));
            array_push($arr, array(
                'value'     => $this->calculator->getArr(),
                'timestamp' => $date->getTimestamp()
            ));
            array_push($arpu, array(
                'value'     => $this->calculator->getArpu(),
                'timestamp' => $date->getTimestamp()
            ));
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
     * @param array $dataSet
     * @return array
    */
    private function sortByDate($dataSet) {
        $timestamps = array();
        foreach($dataSet as $key=>$data) {
            $timestamps[$key] = $data['timestamp'];
        }
        array_multisort($timestamps, SORT_ASC, $dataSet);
        return $dataSet;

    }

    /**
     * Filtering subscriptions to relevant only.
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
     * Trying to mirror the specific date, to our DB.
     *
     * @param date The date on which we're mirroring.
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
     * Handling subscription deletion.
     *
     * @param subscription $subscription
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
     * Handling subscription creation.
     *
     * @param Subscription $subscription
    */
    private function handleSubscriptionCreation($subscription) {
        BraintreeSubscription::where('subscription_id', $subscription->id)->first()->delete();
    }
}