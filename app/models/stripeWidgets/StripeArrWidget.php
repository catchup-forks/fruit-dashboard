<?php

class StripeArrWidget extends HistogramWidget
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user());
        return $stripeCalculator->getArr(TRUE);
    }

}
?>
