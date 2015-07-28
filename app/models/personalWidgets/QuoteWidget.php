<?php

class QuoteWidget extends Widget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'type' => array(
            'name'       => 'Type',
            'type'       => 'SCHOICE',
            'default'    => 'insp'
        ),
        'update_frequency' => array(
            'name'    => 'Update frequency',
            'type'    => 'INT',
            'default' => 1440
        ),
    );
    
    /* The settings to setup in the setup-wizard */
    public static $setupSettings = array();
    public static $dataRequired = TRUE;

    /* Choices functions */
    public function type() {
        return array(
            'insp' => 'Inspirational',
        );
    }

    /**
     * collectData
     * --------------------------------------------------
     * Retrieves data from a google spreadsheet and saves to db
     * @return None
     * --------------------------------------------------
     */
    public function collectData() {
        /* Getting the JSON from GoogleSpreadsheet. */
        $file = file_get_contents($this->getQuoteSpreadsheetUri());
        $decoded_data = json_decode($file);

        /* Not updating if there was no answer. */
        if (is_null($decoded_data)) {
            return;
        }

        /* Select a random row. */
        $quotes = $decoded_data->{'feed'}->{'entry'};
        $key = array_rand($quotes);
        $quote = $quotes[$key];
        $this->data->raw_value = json_encode(array(
            'quote'    => $quote->{'gsx$quote'}->{'$t'},
            'author'   => $quote->{'gsx$author'}->{'$t'},
            'type'     => $quote->{'gsx$type'}->{'$t'},
            'language' => $quote->{'gsx$language'}->{'$t'}
        ));

        /* Save quote */
        $this->data->save();
    }

    /**
     * getData
     * --------------------------------------------------
     * Returns the quote in an assoc array.
     * @return (array) ($quote) The quote and author
     * --------------------------------------------------
     */
    public function getData() {
        $quote = json_decode($this->data->raw_value, 1);
        if (empty($quote)) {
            return array(
                'quote'  => 'Connection error, please try to refresh the widget.',
                'author' => 'Server');
        }
        return $quote;
    }

    /**
     * save
     * --------------------------------------------------
     * Overrides save to request a new quote.
     * @return None
     * --------------------------------------------------
     */
    public function save(array $options=array()) {
        parent::save();
        $this->collectData();
    }

    /**
     * getQuoteSpreadsheetUri
     * --------------------------------------------------
     * Overrides save to request a new quote.
     * @return None
     * --------------------------------------------------
     */
    private function getQuoteSpreadsheetUri() {
        /* Get base url */
        $uri = $_ENV['QUOTE_FEED_CONNECT_URI'];
        
        /* Get spreadsheet based on type */
        switch ($this->getSettings()['type']) {
            case 'insp': 
                $uri .= $_ENV['QUOTE_FEED_SPREADSHEET_INSPIRATIONAL_URI']; 
                break;
            default: 
                $uri .= $_ENV['QUOTE_FEED_SPREADSHEET_INSPIRATIONAL_URI']; 
                break;
        }

        /* Return URI */
        return $uri;
   }

}

?>
