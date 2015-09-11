<?php

class StripeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'stripe_arpu'   => '{"col":4,"row":1,"size_x":6,"size_y":6}',
        'stripe_arr'    => '{"col":2,"row":7,"size_x":5,"size_y":5}',
        'stripe_mrr'    => '{"col":7,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'stripe';
    /* /LATE STATIC BINDING. */

}