<?php

trait DefaultAjaxWidgetTrait
{
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
            /* Get state query signal */
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
}