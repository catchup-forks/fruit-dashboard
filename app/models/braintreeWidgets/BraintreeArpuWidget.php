<?php

class BraintreeArpuWidget extends FinancialWidget
{
    public getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user());
        return $braintreeCalculator->getArr(TRUE);
    }


}
?>
