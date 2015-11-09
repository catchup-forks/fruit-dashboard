<?php

class BraintreeArpuDataCollector extends HistogramDataCollector
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user);
        return $braintreeCalculator->getArpu(true);
    }

}
?>
