<?php

class BraintreeMrrDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user);
        return $braintreeCalculator->getMrr(TRUE);
    }

}
?>
