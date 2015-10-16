<?php

class TwitterMentionsDataManager extends ArticleDataManager
{
    /**
     * collectData
     * Creating data for the widget.
     * @param array $options.
     */
    public function collectData($options=array()) {
        if (array_key_exists('count', $options)) {
            $count = $options['count'];
        } else {
            $count = 5;
        }
        $this->clearData();
        foreach ($this->getMentions($count) as $mention) {
            $article = array(
                'title'    => '@' . $mention->user->screen_name,
                'text'     => $mention->text,
                'created'  => Carbon::parse($mention->created_at)->toDateTimeString(),
                'hashtags' => $mention->entities->hashtags,
                'mentions' => $mention->entities->user_mentions,
                'id'       => $mention->id_str,
                'name'     => $mention->user->name
            );
            $this->addArticle($article);
        }
    }

    /**
     * getMentions
     * Returning the mentions from twitter.
     * --------------------------------------------------
     * @param $count
     * @return TwitterDataCollector
     * --------------------------------------------------
     */
    private function getMentions($count) {
        $collector = new TwitterDataCollector($this->user);
        return $collector->getMentions($count);
    }

    /**
     * isValidArticle
     * Returns whether or not the article is valid.
     * --------------------------------------------------
     * @param array $article
     * @return boolean
     * --------------------------------------------------
     */
    protected static function isValidArticle($article) {
        $valid = parent::isValidArticle($article);
        if ( ! $valid) {
            return FALSE;
        }
        if (array_key_exists('hashtags', $article) && array_key_exists('created', $article) && array_key_exists('id', $article)) {
            return TRUE;
        }
        return FALSE;
    }
}