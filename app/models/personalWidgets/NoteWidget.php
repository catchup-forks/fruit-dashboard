<?php

class NoteWidget extends Widget implements iAjaxWidget
{
    /**
     * getData
     * --------------------------------------------------
     * Returning widget data
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function getData() {
        return json_decode($this->data->raw_value)->text;
    }

    /**
     * createDataScheme
     * --------------------------------------------------
     * Returning a deafult scheme for the data.
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function createDataScheme() {
        return json_encode(array('text'=>''));
    }

    /**
     * handleAjax
     * --------------------------------------------------
     * Handling ajax request, aka saving text.
     * @param $postData the data from the request.
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function handleAjax($postData) {
        $this->saveData($postData['text']);
    }

    /**
     * saveData
     * --------------------------------------------------
     * Saving text to db.
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function saveData($text) {
        $this->data->raw_value = json_encode(array('text' => $text));
        $this->data->save();
    }
}

?>
