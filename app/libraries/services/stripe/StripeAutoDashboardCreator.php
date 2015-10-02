<?php

class StripeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $widgets = array(
        'stripe_mrr'  => array(),
        'stripe_arr'  => array(),
        'stripe_arpu' => array(),
    );
    protected static $service = 'stripe';
    /* /LATE STATIC BINDING. */

}