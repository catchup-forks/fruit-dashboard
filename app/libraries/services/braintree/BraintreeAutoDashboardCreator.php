<?php

class BraintreeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'braintree_mrr'  => '{"col":4,"row":1,"size_x":6,"size_y":6}',
        'braintree_arr'  => '{"col":2,"row":7,"size_x":5,"size_y":5}',
        'braintree_arpu' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'braintree';
    /* /LATE STATIC BINDING. */

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
     * Setting up the calculator.
     */
    protected function setup($args) {
        $this->calculator = new BraintreeCalculator($this->user);
        $this->subscriptions = $this->calculator->getCollector()->getAllSubscriptions();
        $this->filterSubscriptions();
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateDashboard() {
        $mrrWidget  = $this->widgets['braintree_mrr'];
        $arrWidget  = $this->widgets['braintree_arr'];
        $arpuWidget = $this->widgets['braintree_arpu'];

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
     * @param array $dataSet
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