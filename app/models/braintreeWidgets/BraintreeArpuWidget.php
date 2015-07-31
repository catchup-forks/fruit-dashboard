<?php

class BraintreeArpuWidget extends FinancialWidget
{
    public function getCurrentValue() {
        $braintreeCalculator = new BraintreeCalculator($this->user());
        return $braintreeCalculator->getArr(TRUE);
    }


}
?>
