<?php

class QuoteWidget extends Widget
{
    public static $type = 'quote';

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
    // The settings to setup in the setup-wizard.
    public static $setupSettings = array();
    public static $dataRequired = TRUE;

    /* Choices functions */
    public function type() {
        return array(
            'insp' => 'Inspirational',
        );
    }

    private function getSpreadsheetUri() {
        $uri = 'http://spreadsheets.google.com/feeds/list/';
        switch ($this->getSettings()['type']) {
            case 'insp': $uri .= '1Xqp_INZG92NUKcL6F9BwcPrDpht0XNLdYLLugZhATbM/od6/public/values'; break;
            default:;
        }
        $uri .= '?alt=json';

        return $uri;
    }

    /**
     * collectData
     * --------------------------------------------------
     * Retrieving data from a google spreadsheet,
     * and saving to db.
     * --------------------------------------------------
     */
    public function collectData() {
        /* Getting the JSON from GoogleSpreadsheet. */
        $file = file_get_contents($this->getSpreadsheetUri());
        $decoded_data = json_decode($file);
        if (is_null($decoded_data)) {
            /* Not updating if there was no answer. */
            return;
        }

        // Making sure we have data.
        /* Selecting a random row. */
        $quotes = $decoded_data->{'feed'}->{'entry'};
        $key = array_rand($quotes);
        $quote = $quotes[$key];
        $this->data->raw_value = json_encode(array(
            'quote'    => $quote->{'gsx$quote'}->{'$t'},
            'author'   => $quote->{'gsx$author'}->{'$t'},
            'type'     => $quote->{'gsx$type'}->{'$t'},
            'language' => $quote->{'gsx$language'}->{'$t'}
        ));

        $this->data->save();
    }

    /**
     * getData
     * --------------------------------------------------
     * Returning data in an assoc array.
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

    public function save(array $options=array()) {
        parent::save();
        $this->collectData();
    }

}

?>
