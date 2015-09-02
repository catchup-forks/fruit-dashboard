<?php

/* This class is responsible for data collection. */
class StripeMrrDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user);
        return $stripeCalculator->getMrr(TRUE);
    }
}