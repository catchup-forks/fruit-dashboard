<?php

class StripeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'stripe_mrr'  => '{"col":1,"row":1,"size_x":4,"size_y":4}',
        'stripe_arr'  => '{"col":5,"row":1,"size_x":4,"size_y":4}',
        'stripe_arpu' => '{"col":9,"row":1,"size_x":4,"size_y":4}',
    );
    protected static $service = 'stripe';
    /* /LATE STATIC BINDING. */

}