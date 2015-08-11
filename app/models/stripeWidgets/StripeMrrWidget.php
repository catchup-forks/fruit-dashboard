<?php

class StripeMrrWidget extends HistogramWidget
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user());
        return $stripeCalculator->getMrr(TRUE);
    }

}
?>
