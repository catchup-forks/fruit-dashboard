<?php

class NoteWidget extends DataWidget implements iAjaxWidget
{

    protected function createDataScheme() {
        return array('text' => '');
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
        $this->data->raw_value = json_encode(array('text' => $postData['text']));
        $this->data->save();
    }
}

?>
