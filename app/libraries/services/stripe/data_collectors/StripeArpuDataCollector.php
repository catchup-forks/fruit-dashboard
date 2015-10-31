<?php


/* This class is responsible for data collection. */
class StripeArpuDataCollector extends HistogramDataCollector
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user);
        return $stripeCalculator->getArpu(TRUE);
    }
}
