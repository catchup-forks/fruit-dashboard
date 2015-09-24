<?php

class Plan extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
    );

    protected $fillable = array(
        'name',
        'description',
        'amount',
        'braintree_plan_id',
        'braintree_merchant_account_id',
        'braintree_merchant_currency',
    );

    /* -- No timestamps -- */
    public $timestamps = false; 

    /* -- Relations -- */
    public function subscriptions() { return $this->hasMany('Subscription'); }


    /**
     * ================================================== *
     *                PUBLIC STATIC SECTION               *
     * ================================================== *
     */

    /**
     * getFreePlan
     * --------------------------------------------------
     * @return (Plan) ($freePlan) Returns the free plan
     * --------------------------------------------------
     */
    public static function getFreePlan() {
        /* Get the free plan */
        $freePlan = Plan::where('name', 'Free')->first();
        
        /* Return */
        return $freePlan;
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * isFree
     * --------------------------------------------------
     * @return (boolean) ($isFree) Returns true if the Plan is the free plan
     * --------------------------------------------------
     */
    public function isFree() {       
        /* Return */
        return $this->id == self::getFreePlan()->id;
    }

}
