<?php

class FacebookNewLikesDataManager extends GeneralFacebookDataManager
{
    public function getCurrentValue() {
        $collector = new FacebookDataCollector($this->user);
        /* Getting previous last data. */
        $lastData = $this->getLikesManager()->getLatestData();
        if (is_null($lastData)) {
            return $collector->getTotalLikes($this->getPage());
        }
        else {
            return $collector->getTotalLikes($this->getPage()) - $lastData['value'];
        }
    }

    /**
     * getLikesManager
     * --------------------------------------------------
     * @return One of the user's facebook likes widget.
     * --------------------------------------------------
     */
    private function getLikesManager() {
        foreach ($this->user->dataManagers as $dataManager) {
            if (($dataManager->descriptor->type == 'facebook_likes') && ($this->getCriteria() == $dataManager->getCriteria())) {
                return $dataManager->getSpecific();
            }
        }
        return null;
    }

}
?>
