<?php

class StripeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'stripe_mrr'  => '{"col":1,"row":1,"size_x":6,"size_y":6}',
        'stripe_arr'  => '{"col":1,"row":7,"size_x":6,"size_y":6}',
        'stripe_arpu' => '{"col":7,"row":1,"size_x":6,"size_y":6}',
    );
    protected static $service = 'stripe';
    /* /LATE STATIC BINDING. */

}