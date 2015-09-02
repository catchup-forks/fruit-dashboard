<?php

class StripeArrDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user);
        return $stripeCalculator->getArr(TRUE);
    }

}
?>
