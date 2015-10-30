<?php

class NoteWidget extends DataWidget implements iAjaxWidget
{
    /**
     * getTemplateData
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'data' => $this->getData()
        ));
    }

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

    public function refreshWidget() {}
}

?>
