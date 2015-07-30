<?php

class StripeArrWidget extends FinancialWidget
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user());
        return $stripeCalculator->getArr(TRUE);
    }

}
?>
