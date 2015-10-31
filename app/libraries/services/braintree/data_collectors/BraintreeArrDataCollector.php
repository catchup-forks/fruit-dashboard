<?php

class BraintreeArrDataCollector extends HistogramDataCollector
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user);
        return $braintreeCalculator->getArr(TRUE);
    }

}
?>
