<?php


/* This class is responsible for data collection. */
class StripeArrDataCollector extends HistogramDataCollector
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user);
        return $stripeCalculator->getArr(TRUE);
    }
}
