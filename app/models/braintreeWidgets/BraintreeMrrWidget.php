<?php

class BraintreeMrrWidget extends HistogramWidget
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user());
        return $braintreeCalculator->getArr(TRUE);
    }

}
?>
