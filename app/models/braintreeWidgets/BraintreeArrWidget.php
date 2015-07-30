<?php

class BraintreeArrWidget extends FinancialWidget
{
    public getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user());
        return $braintreeCalculator->getArr(TRUE);
    }

}
?>
