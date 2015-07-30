<?php

class StripeArpuWidget extends FinancialWidget
{

    public function collectData() {
        $currentData = $this->getHistogram();
        try {
            $stripeCalculator = new StripeCalculator($this->user());
            array_push($currentData, $stripeCalculator->getArpu(TRUE));
            $this->data->raw_value = json_encode($currentData);
            $this->data->save();
        } catch (StripeNotConnected $e) {
            ;
        }
        $this->checkIntegrity();
    }
}
?>
