<?php

class FacebookNewLikesDataManager extends HistogramDataManager
{
    use FacebookDataManagerTrait;
    public function getCurrentValue() {
        $collector = new FacebookDataCollector($this->user);
        /* Getting previous last data. */
        $lastData = $this->getLikesManager()->getLatestValues();
        $page = $this->getPage();
        if (is_null($lastData)) {
            return $collector->getTotalLikes($page->id);
        }
        else {
            return $collector->getTotalLikes($page->id) - $lastData['value'];
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
