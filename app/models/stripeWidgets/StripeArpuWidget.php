<?php

class StripeArpuWidget extends FinancialWidget
{

    public function getCurrentValue() {
        $stripeCalculator = new StripeCalculator($this->user());
        return $stripeCalculator->getArpu(TRUE);
    }
}
?>
