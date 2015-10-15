<?php

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\GraphUser;
/**
* --------------------------------------------------------------------------
* FacebookDataCollector:
*       Getting data from facebook account.
* --------------------------------------------------------------------------
*/

class FacebookDataCollector
{
    /* -- Class properties -- */
    /**
     * @var User The user entity for this collector.
     *
     */
    private $user;

    /**
     * @var string access token
     *
     */
    private $accessToken;

    /**
     * @var Facebook The Facebook entity.
     *
     */
    private $fb;

    /**
     * @var FacebookApp The Facebook app entity.
     *
     */
    private $app;


    /* -- Constructor -- */
    function __construct($user, $connector=null) {
        $this->user = $user;
        if (is_null($connector)) {
            $connector = new FacebookConnector($user);
        }
        $this->accessToken = $connector->connect();
        $this->fb = $connector->getFB();
        $this->app = $this->fb->getApp();
    }

    /**
     * getUserID
     * Returns the user's id.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getUserID() {
        try {
            $response = $this->fb->get('/me/?fields=id', $this->accessToken);
        } catch (FacebookResponseException $e) {
            return null;
        } catch (FacebookSDKException $e) {
            return null;
        }
        return $response->getGraphUser()['id'];
    }

    /**
     * savePages
     * Saves The user's pages
     * --------------------------------------------------
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function savePages() {
        $userId = $this->getUserID();
        if (is_null($userId)) {
            throw new ServiceException("We couldn't find your facebook profile.", 1);
            ;
        }
        try {
            $response = $this->fb->get('/' . $userId . '/accounts', $this->accessToken);
        } catch (Exception $e) {
            throw new ServiceException("Error Processing Request", 1);
        }

        /* Saving pages. */
        $pages = array();
        foreach ($response->getGraphEdge() as $graphNode) {
            $page = new FacebookPage(array(
                'id'   => $graphNode['id'],
                'name' => $graphNode['name']
            ));
            $page->user()->associate($this->user);
            array_push($pages, $page);
        }

        if (count($pages) > 0) {
            /* Only refreshing if we have results. */
            $this->user->facebookPages()->delete();
            foreach ($pages as $page) {
                $page->save();
            }
        }

        return $pages;
    }

    /**
     * getInsightCurrentValue
     * Returning the current value of an insight.
     * --------------------------------------------------
     * @param int $page
     * @param string $insight
     * @param string $period
     * @return numeric
     * @throws ServiceException
     * --------------------------------------------------
    */
    public function getInsightCurrentValue($pageId, $insight, $period) {
        $insightData = $this->getInsight(
            $insight, $pageId,
            array('period' => $period)
        );
        return end($insightData[0]['values'])['value'];
    }

    /**
     * getPopulateHistogram
     * Returning histogram values for connector back.
     * --------------------------------------------------
     * @param int $pageId
     * @param string $insight
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
    */
    public function getPopulateHistogram($pageId, $insight) {
        return $this->getInsight(
            $insight, $pageId,
            array(
                'since' => Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['facebook'])->getTimestamp(),
                'until' => Carbon::now()->getTimestamp(),
            )
        );
    }

    /**
     * getInsight
     * Getting the specific insight.
     * --------------------------------------------------
     * @param int $pageId
     * @param string $insight
     * @param array $params
     * @return array
     * @throws ServiceException
     * --------------------------------------------------
    */
    private function getInsight($insight, $pageId, $params=array()) {
        $paramstr = '?';
        foreach ($params as $key=>$value) {
            $paramstr .= '&' . $key . '='. $value;
        }
        try {
            $response =  $this->fb->get('/' . $pageId . '/insights/' . $insight . $paramstr , $this->accessToken);
            return $response->getDecodedBody()['data'];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Facebook connection error.", 1);
        }
    }

} /* FacebookDataCollector */
