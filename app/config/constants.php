<?php
//file : app/config/constants.php

return array(
    /* ------------------- CURRENCY FONT/TEXT ------------------- *
     * stripe uses iso 4217 codes, http://www.xe.com/symbols.php  *
     * ---------------------------------------------------------- */
    'usd' => '$',
    'aud' => '$',
    'cny' => '¥',
    'huf' => 'Ft',
    'cad' => '$',
    'chf' => 'CHF',
    'jpy' => '¥',
    'gbp' => '£',
    'eur' => '€',

    /* ---------------------- COUNTRY CODES --------------------- *
     * stripe uses iso 3166-1 alpha-2 codes php uses full locales *
     * ---------------------------------------------------------- */
    'US' => 'en_US',

     /* --------------- STRIPE RELATED --------------- */
    'STRIPE_SECRET_KEY'       => 'sk_test_wizVjDXOcT6iQelpaMVpYwDD',
    'STRIPE_CONNECT_URI'      => 'https://connect.stripe.com/oauth/authorize',
    'STRIPE_ACCESS_TOKEN_URI' => 'https://connect.stripe.com/oauth/token',
    'STRIPE_CLIENT_ID'        => 'ca_6bePhtk1mE54xai3CJrZ5lXu2uaciboU',

     /* --------------- WIDGET RELATED --------------- */
     'WIDGET_TYPES' => array('clock', 'quote'),
);

