<?php

class FacebookNewLikesDataManager extends GeneralFacebookDataManager
{
    public function getCurrentValue() {
        $collector = new FacebookDataCollector($this->user);
        /* Getting previous last data. */
        $lastData = $this->getLikesCollector()->getLatestData();
        if (is_null($lastData)) {
            return $collector->getTotalLikes($this->getPage());
        }
        else {
            return $collector->getTotalLikes($this->getPage()) - $lastData['value'];
        }
    }

    /**
     * getLikesCollector
     * --------------------------------------------------
     * @return One of the user's facebook likes widget.
     * --------------------------------------------------
     */
    private function getLikesCollector() {
        foreach ($this->user->dataManagers as $dataManager) {
            if (($dataManager->descriptor->type == 'facebook_likes') && ($this->getCriteria() == $dataManager->getCriteria())) {
                return $dataManager->getSpecific();
            }
        }
        return null;
    }

}
?>
