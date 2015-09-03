<?php

class BraintreeArrDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user);
        return $braintreeCalculator->getArr(TRUE);
    }

}
?>
