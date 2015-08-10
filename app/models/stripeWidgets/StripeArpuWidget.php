<?php

class StripeArpuWidget extends HistogramWidget
{

    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user());
        return $stripeCalculator->getArpu(TRUE);
    }
}
?>
