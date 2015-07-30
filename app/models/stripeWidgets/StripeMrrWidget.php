<?php

class StripeMrrWidget extends FinancialWidget
{
    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user());
        return $stripeCalculator->getMrr(TRUE);
    }

}
?>
