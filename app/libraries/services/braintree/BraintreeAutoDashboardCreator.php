<?php

class BraintreeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'braintree_mrr'  => '{"col":1,"row":1,"size_x":4,"size_y":4}',
        'braintree_arr'  => '{"col":5,"row":1,"size_x":4,"size_y":4}',
        'braintree_arpu' => '{"col":9,"row":1,"size_x":4,"size_y":4}',
    );
    protected static $service = 'braintree';
    /* /LATE STATIC BINDING. */

}