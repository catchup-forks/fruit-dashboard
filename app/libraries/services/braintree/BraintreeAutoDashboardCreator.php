<?php

class BraintreeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* LATE STATIC BINDING. */
    protected static $widgets = array(
        'braintree_mrr'  => array(),
        'braintree_arr'  => array(),
        'braintree_arpu' => array(),
    );
    protected static $service = 'braintree';
    /* /LATE STATIC BINDING. */
}