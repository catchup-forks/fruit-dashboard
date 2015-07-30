<?php

class StripeMrrWidget extends FinancialWidget
{

    public function collectData() {
        $currentData = $this->getHistogram();
        try {
            $stripeCalculator = new StripeCalculator($this->user());
        } catch (StripeNotConnected $e) {
            ;
        }
        array_push($currentData, $stripeCalculator->getMrr(TRUE));
        $this->data->raw_value = json_encode($currentData);
        $this->data->save();
        $this->checkIntegrity();
    }
}
?>
