<?php

class StripeLastMonthCalculator extends StripeCalculator{
    /* -- Class properties -- */
    public static $allowedEventTypes = array(
        'customer.subscription.created',
        'customer.subscription.updated',
        'customer.subscription.deleted'
    );
    private $events;

    /* -- Constructor -- */
    function __construct($user) {
        parent::__construct($user);
        $this->events = $this->dataCollector->getEvents();
        $this->filterEvents();
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getLastMonthData
     * --------------------------------------------------
     * Returning all metrics in an array.
     * @return All metrics in an array.
     * --------------------------------------------------
    */
    public function getLastMonthData() {
        /* Updating subscriptions to be up to date. */
        $this->dataCollector->updateSubscriptions();

        $mrr = array();
        $arr = array();
        $arpu = array();

        for ($i = 0; $i < 30; $i++) {
            /* Calculating the date to mirror. */
            $date = Carbon::now()->subDays($i)->toDateString();
            $this->mirrorDay($date);
            array_push($mrr, array('date' => $date, 'value' => $this->getMrr()));
            array_push($arr, array('date' => $date, 'value' => $this->getArr()));
            array_push($arpu, array('date' => $date, 'value' => $this->getArpu()));
        }

        return array('mrr' => $mrr, 'arr' => $arr, 'arpu' => $arpu);
    }

    /**
     * ================================================== *
     *                  PRIVATE SECTION                   *
     * ================================================== *
     */

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
     * @date The date on which we're mirroring.
     * --------------------------------------------------
    */
    private function mirrorDay($date) {
        foreach ($this->events as $key=>$event) {
            $eventDate = Carbon::createFromTimestamp($event['created'])->toDateString();
            if ($eventDate == $date) {
                switch ($event['type']) {
                    case 'customer.subscription.created': $this->handleSubscriptionCreation($event); break;
                    case 'customer.subscription.updated': $this->handleSubscriptionUpdate($event); break;
                    case 'customer.subscription.deleted': $this->handleSubscriptionDeletion($event); break;
                    default:;
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
     * @event The specific stripe event.
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
     * @event The specific stripe event.
     * --------------------------------------------------
    */
    private function handleSubscriptionUpdate($event) {
        Log::info("update");
    }

    /**
     * handleSubscriptionCreation
     * --------------------------------------------------
     * Handling subscription creation.
     * On creation we'll have to delete a subscription.
     * @event The specific stripe event.
     * --------------------------------------------------
    */
    private function handleSubscriptionCreation($event) {
        $subscriptionData = $event['data']['object'];
        StripeSubscription::where('subscription_id', $subscriptionData['id'])->first()->delete();
    }
}