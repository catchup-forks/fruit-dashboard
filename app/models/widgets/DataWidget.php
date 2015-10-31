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
        if ( ! $this->hasValidCriteria()) {
            return;
        }
        /* Assigning the data. */
        foreach ($this->getDataObjects() as $dataObject) {
            $this->data[$dataObject->type] = $dataObject->decode();
        }
    }

    /**
     * getDataObjects
     * Return the corresponding data objects.
     * --------------------------------------------------
     * @param array $attributes
     * --------------------------------------------------
     */
    private function getDataObjects() {
        $dataObjects = array();
        $widgetCriteria = $this->getCriteria();

        /* Getting the corresponding data objects, with one optimized query. */
        foreach ($this->user()->dataObjects()
            ->join('data_descriptors', 'data_descriptors.id', '=' , 'data.descriptor_id')
            ->where('data_descriptors.category', $this->getDescriptor()->category)
            ->whereIn('data_descriptors.type', static::getDataTypes())
            ->get(array('data.id', 'data.criteria', 'data_descriptors.type')) as $dataObject) {
            /* Filtering criteria. */
            $dataCriteria = $dataObject->getCriteria();
            if (count(array_intersect($dataCriteria, $widgetCriteria)) ==
                count($dataCriteria)) {
                array_push($dataObjects, $dataObject);
            }
        }

        if (count($dataObjects) != count(static::getDataTypes())) {
            throw new WidgetException('Insuficcient data available.');
        }

        return $dataObjects;
    }


}
