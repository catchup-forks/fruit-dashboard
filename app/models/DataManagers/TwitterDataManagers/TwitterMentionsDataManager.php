<?php

class TwitterMentionsDataManager extends ArticleDataManager
{
    /**
     * collectData
     * Creating data for the widget.
     */
    public function collectData($options=array()) {
        $this->clearData();
        foreach ($this->getMentions() as $mention) {
            $article = array(
                'title' => '@' . $mention->user->screen_name,
                'text'  => $mention->text
            );
            $this->addArticle($article);
        }
    }

    /**
     * getMentions
     * Returning the mentions from twitter.
     * --------------------------------------------------
     * @return TwitterDataCollector
     * --------------------------------------------------
     */
    private function getMentions() {
        $collector = TwitterDataCollector($this->user);
        return $collector->getMentions();
    }
}