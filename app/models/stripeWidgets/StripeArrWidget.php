<?php

class StripeArrWidget extends FinancialWidget
{
    /* -- Table specs -- */
    public static $type = 'stripe_arr';

    public function collectData() {
        $currentData = $this->getHistogram();
        try {
            $stripeCalculator = new StripeCalculator($this->user());
            array_push($currentData, $stripeCalculator->getArr(TRUE));
            $this->data->raw_value = json_encode($currentData);
            $this->data->save();
        } catch (StripeNotConnected $e) {
            ;
        }
        $this->checkIntegrity();
    }
}
?>
