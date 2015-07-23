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

    /* ---------------------- TRIAL PERIOD ---------------------- *
     * ---------------------------------------------------------- */
    'TRIAL_PERIOD_IN_DAYS' => 14,

    /* ---------------------- PLAN RELATED ---------------------- *
     * ---------------------------------------------------------- */
    'PLAN_ID_CONTRIBUTE' => 1,
    'PLAN_ID_FREE'       => 2,
    'PLAN_ID_PREMIUM'    => 3,

    /* --------------- WIDGET DESCRIPTOR RELATED ---------------- *
     * ---------------------------------------------------------- */
    'WD_ID_CLOCK'       => 1,
    'WD_ID_QUOTES'      => 2,
    'WD_ID_GREETINGS'   => 3,

    'WD_ID_STRIPE_MRR'   => 4,

);

