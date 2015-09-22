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
     *                   CLASS ATTRIBUTES                 *
     * ================================================== *
     */
    private static $apiVersions = array('1.0');

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */
    
    /**
     * anyPostData
     * --------------------------------------------------
     * @return Handles the incoming POST request, and checks its integrity
     * --------------------------------------------------
     */
    public function anyPostData($apiVersion = null, $apiKey = null, $widgetID = null) {
        /* Check API version */
        if (!in_array($apiVersion, self::$apiVersions)) {
            return Response::json(array('error' => 'This API version is not supported.'));
        }

        /* Call API hadler */
        $status = $this->handlePostData($apiVersion, $apiKey, $widgetID);

        /* Return based on status */
        if ($status['is_success']) {
            return Response::json(array('success' => 'Your data has been posted successfully.'));
        } else {
            return Response::json(array('error' => $status['message']));
        }
    }

    /**
     * anyExample
     * --------------------------------------------------
     * @return Renders the example page
     * --------------------------------------------------
     */
    public function anyExample() {
        /* Create default JSON string */
        $defaultJSON = 
            "{\n".
            "'date':'" . Carbon::now()->toDateString(). "', \n" .
            "'timestamp':" . Carbon::now()->getTimestamp(). ", \n" .
            "'Graph One': 15, \n" .
            "'Graph Two': 40 \n" .
            "}";

        /* Render view */
        return View::make('api.example', 
                          array('apiVersion' => end(self::$apiVersions),
                                'apiKey'     => Auth::user()->api_key,
                                'widgetID'   => '-WidgetID-',
                                'defaultJSON'=> $defaultJSON));
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
                    return array('is_success' => FALSE,
                                 'message'    => 'Your API key is invalid.');
                }

                /* Check Widget ID */
                if (is_null(Widget::where('id', $widgetID)->first())) {
                    return array('is_success' => FALSE,
                                 'message'    => 'Your Widget ID is invalid.');
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
        /* Initialize status */
        $status = array();

        $status['is_success'] = TRUE;

        return $status;
    }
}
