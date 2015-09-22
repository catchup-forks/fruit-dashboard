<?php

//md5(uniqid($your_user_login, true))
//base64_encode 
/**
 * --------------------------------------------------------------------------
 * APIController: Handles the Fruit Dashboard API
 * --------------------------------------------------------------------------
 */
class APIController extends BaseController
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */
    
    /**
     * postData
     * --------------------------------------------------
     * @return Handles the incoming POST request, and checks its integrity
     * --------------------------------------------------
     */
    public function postData($apiVersion = null, $apiKey = null, $widgetID = null) {
        /* Check API version */
        if (!in_array($apiVersion, SiteConstants::getApiVersions())) {
            return Response::json(array('status'  => FALSE,
                                        'message' => 'This API version is not supported.'));
        }

        /* Call API hadler */
        $result = $this->handlePostData($apiVersion, $apiKey, $widgetID);

        /* Return based on result */
        return Response::json($result);
    }

    /**
     * getTest
     * --------------------------------------------------
     * @return Renders the example page
     * --------------------------------------------------
     */
    public function getTest($widgetID) {
        /* Get the requested widget */
        $widget = Widget::where('id', $widgetID)->first();

        /* Get the widget API url */
        $url = $widget->getSpecific()->dataManager()->getCriteria()['url'];

        /* Create default JSON string */
        $defaultJSON = 
            "{\n".
            "'date':'" . Carbon::now()->toDateString(). "', \n" .
            "'timestamp':" . Carbon::now()->getTimestamp(). ", \n" .
            "'Graph One': 15, \n" .
            "'Graph Two': 40\n" .
            "}";

        /* Render view */
        return View::make('api.test', 
                            ['url'        => $url,
                            'defaultJSON' => $defaultJSON]);
    }


    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */
    
    /**
     * handlePostData
     * --------------------------------------------------
     * @return Saves the data to the database
     * --------------------------------------------------
     */
    private function handlePostData($apiVersion, $apiKey, $widgetID) {
        /* Handle POST data based on the API version */
        switch ($apiVersion) {
            case '1.0':
            default:
                /* Check API key */
                if (is_null(User::where('api_key', $apiKey)->first())) {
                    return array('status'  => FALSE,
                                 'message' => 'Your API key is invalid.');
                }

                /* Check Widget ID */
                if (is_null(Widget::where('id', $widgetID)->first())) {
                    return array('status'  => FALSE,
                                 'message' => 'Your Widget ID is invalid.');
                }

                return $this->saveWidgetData(
                            User::where('api_key', $apiKey)->first(), 
                            Widget::where('id', $widgetID)->first()
                       );
                break;
        }
    }

    /**
     * saveWidgetData
     * --------------------------------------------------
     * @return Saves the data to the database
     * --------------------------------------------------
     */
    private function saveWidgetData($user, $widget) {
        /* Initialize result */
        $result = array();

        $result['status'] = TRUE;
        $result['message'] = 'Your data has been successfully saved.';

        return $result;
    }
}
