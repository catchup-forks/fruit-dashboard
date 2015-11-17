<?php

class BraintreeMrrDataCollector extends HistogramDataCollector
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user);
        return $braintreeCalculator->getMrr(true);
    }

}
?>
