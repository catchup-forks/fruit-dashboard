<?php

class StripePopulateData
{
    /* -- Class properties -- */
    const DAYS = 30;

    private static $allowedEventTypes = array(
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
        Log::info("Starting Stripe data collection for user #". $this->user->id . " at " . Carbon::now()->toDateTimeString());
        $this->calculator = new StripeCalculator($this->user);
        $this->events = $this->calculator->getCollector()->getEvents();
        $this->filterEvents();
        $this->dataManagers = $this->getManagers();
        $this->populateData();
        Log::info("Stripe data collection finished and it took " . (microtime($time) - $time) . " seconds to run.");
        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        /* Creating data for the last DAYS days. */
        $metrics = $this->getMetrics();

        $this->dataManagers['stripe_mrr']->saveData($metrics['mrr']);
        $this->dataManagers['stripe_arr']->saveData($metrics['arr']);
        $this->dataManagers['stripe_arpu']->saveData($metrics['arpu']);

        foreach ($this->dataManagers as $manager) {
            $manager->setWidgetsState('active');
        }
    }

    /**
     * Getting the DataManagers
     * @return array
     */
    private function getManagers() {
        $dataManagers = array();

        foreach ($this->user->dataManagers()->get() as $generalDataManager) {
            $dataManager = $generalDataManager->getSpecific();
            if ($dataManager->descriptor->category == 'stripe') {
                /* Setting dataManager. */
                $dataManagers[$dataManager->descriptor->type] = $dataManager;
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
     * @param array dataSet
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
            'trial_end'       => $subscriptionData['trial_end'],
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