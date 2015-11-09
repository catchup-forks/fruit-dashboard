<?php

/* This class is responsible for data collection. */
class StripeMrrDataCollector extends HistogramDataCollector
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user);
        return $stripeCalculator->getMrr(true);
    }
}
