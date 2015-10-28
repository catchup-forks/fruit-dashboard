<?php

class QuoteDataManager extends DataManager
{
    /**
     * collect
     * --------------------------------------------------
     * Retrieves data from a google spreadsheet and saves to db
     * @return None
     * --------------------------------------------------
     */
    public function collect($options=array()) {
        /* Getting the JSON from GoogleSpreadsheet. */
        try {
            $file = file_get_contents($this->getQuoteSpreadsheetUri());
        } catch (Exception $e) {
            throw new ServiceException('Could not update the quotes.');
        }

        $decoded_data = json_decode($file);

        /* Not updating if there was no answer. */
        if (is_null($decoded_data)) {
            return;
        }

        /* Select a random row. */
        $quotes = $decoded_data->{'feed'}->{'entry'};
        $key = array_rand($quotes);
        $quote = $quotes[$key];
        $this->save(array(
            'quote'    => $quote->{'gsx$quote'}->{'$t'},
            'author'   => $quote->{'gsx$author'}->{'$t'},
            'type'     => $quote->{'gsx$type'}->{'$t'},
            'language' => $quote->{'gsx$language'}->{'$t'}
        ));
    }

    /**
     * initialize
     * Initializing the data.
     */
    public function initialize() {
        $this->collect();
    }

    /**
     * getDataScheme
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getDataScheme() {
        return array('quote', 'author');
    }

    /**
     * build
     * --------------------------------------------------
     * Returns the quote in an assoc array.
     * @return (array) ($quote) The quote and author
     * --------------------------------------------------
     */
    public function build($postData=null) {
        $quote = $this->data;
        if (empty($quote)) {
            return array(
                'quote'  => 'Connection error, please try to refresh the widget.',
                'author' => 'Server');
        }
        return $quote;
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
        switch ($this->criteria['type']) {
            case 'inspirational':
                $uri .= $_ENV['QUOTE_FEED_SPREADSHEET_EN_INSPIRATIONAL_URI'];
                break;
            case 'funny':
                $uri .= $_ENV['QUOTE_FEED_SPREADSHEET_EN_FUNNY_URI'];
                break;
            case 'first-line':
                $uri .= $_ENV['QUOTE_FEED_SPREADSHEET_EN_FIRST_LINE_URI'];
                break;
            default:
                $uri .= $_ENV['QUOTE_FEED_SPREADSHEET_EN_INSPIRATIONAL_URI'];
                break;
        }

        /* Return URI */
        return $uri;
   }
}
?>
