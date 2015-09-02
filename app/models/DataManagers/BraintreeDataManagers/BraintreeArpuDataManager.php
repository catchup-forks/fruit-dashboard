<?php

class BraintreeArpuDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user);
        return $braintreeCalculator->getArpu(TRUE);
    }


}
?>
