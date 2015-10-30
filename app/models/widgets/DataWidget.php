<?php

/* All classes that have interaction with data. */
abstract class DataWidget extends Widget implements iAjaxWidget
{
    /**
     * Whether or not the criteria has changed.
     *
     * @var bool
     */
    protected $criteriaChanged = FALSE;

    /**
     * An array of the data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * handleAjax
     * Handling general ajax request.
     * --------------------------------------------------
     * @param array $postData
     * @return mixed
     * --------------------------------------------------
    */
    public function handleAjax($postData) {
        if (isset($postData['state_query']) && $postData['state_query']) {
            /* Got state query signal */
            if ($this->state == 'loading') {
                return array('ready' => FALSE);
            } else if($this->state == 'active') {
                /* Rerendering the widget */
                $view = View::make($this->getDescriptor()->getTemplateName())
                    ->with('widget', $this->getTemplateData());
                return array(
                    'ready' => TRUE,
                    'data'  => $this->getData($postData),
                    'html'  => $view->render()
                );
            } else {
                return array('ready' => FALSE);
            }
        }
        if (isset($postData['refresh_data']) && $postData['refresh_data']) {
            /* Refresh signal */
            try {
                $this->refreshWidget();
            } catch (ServiceException $e) {
                Log::error($e->getMessage());
                return array('status'  => FALSE,
                             'message' => 'We couldn\'t refresh your data, because the service is unavailable.');
            }
        }
    }

    /**
     * updateData
     * Refreshing the widget data.
     * --------------------------------------------------
     * @param array options
     * @return string
     * --------------------------------------------------
    */
    public function updateData(array $options=array())
    {

        /* TODO
        try {
            $this->dataManager->collect($options);
        } catch (ServiceException $e) {
            Log::error('An error occurred during collecting data on #' . $this->data_id );
            $this->dataObject->setState('data_source_error');
        } */
    }

    /**
     * refreshWidget
     * Refreshing the widget data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    protected function refreshWidget()
    {
        $this->setState('loading');

        $this->updateData();

        $this->setState('active');
    }

    /**
     * onCreate
     * Creating dataManager.
     * --------------------------------------------------
     * @param array $attributes
     * --------------------------------------------------
     */
    protected function onCreate()
    {
        /* Running the query. */
        $dataObjects = Data::whereIn('data_descriptors.type', $this->getDataTypes())
            ->where('criteria', json_encode($this->getCriteria()))
            ->where('data_descriptors.category', $this->getDescriptor()->category)
            ->where('user_id', $this->user_id)
            ->get();

        if (count($dataObjects) != count($this->getDataTypes())) {
            throw new WidgetException('Insuficcient data for this widget.');
        }

        foreach ($dataObjects as $dataObject) {
            $this->data[$dataType] = $dataObject->decode();
        }
    }
}
