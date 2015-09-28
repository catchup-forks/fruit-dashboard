<?php

class StripeArpuDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user);
        return $stripeCalculator->getArpu(TRUE);
    }
}
?>
