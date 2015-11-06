<?php

class QuoteWidget extends DataWidget 
{
    /* Data selector. */
    protected static $dataTypes = array('quote');

    /* -- Settings -- */
    private static $quoteSettings = array(
        'type' => array(
            'name'    => 'Type',
            'type'    => 'SCHOICE',
            'default' => 'inspirational',
        ),
    );

    private static $typeSettings = array('type');

    /* Choices functions */
    public function type() {
        return array(
            'inspirational' => 'Inspirational quotes',
            'funny'         => 'Funny quotes',
            'first-line'    => 'First lines from books',
        );
    }

    /**
     * getTemplateData
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'quote' => $this->getQuote()
        ));
    }

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields()
    {
        return array(self::$quoteSettings);
    }

    /**
     * getCriteriaFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getCriteriaFields() {
        return array_merge(parent::getCriteriaFields(), self::$typeSettings);
     }

    /**
     * getSetupFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$typeSettings);
    }

    /**
     * getQuote
     * --------------------------------------------------
     * Returns the quote in an assoc array.
     * @return (array) ($quote) The quote and author
     * --------------------------------------------------
     */
    public function getQuote($postData=null) {
        $quote = $this->data['quote'];
        if (empty($quote)) {
            return array(
                'quote'  => 'Connection error, please try to refresh the widget.',
                'author' => 'Server');
        }
        return $quote;
    }

}

?>
