<?php

class BraintreeArrWidget extends FinancialWidget
{

    public function collectData() {
        $currentData = $this->getHistogram();
        try {
            $braintreeCalculator = new BraintreeCalculator($this->user());
            array_push($currentData, $braintreeCalculator->getArr(TRUE));
            $this->data->raw_value = json_encode($currentData);
            $this->data->save();
        } catch (BraintreeNotConnected $e) {
            ;
        }
        $this->save();
    }
}
?>
