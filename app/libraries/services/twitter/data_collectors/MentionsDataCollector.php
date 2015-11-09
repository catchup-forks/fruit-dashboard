<?php

class MentionsDataCollector extends DataCollector
{
    /**
     * collect
     * Creating data for the widget.
     * --------------------------------------------------
     * @param array $options.
     * --------------------------------------------------
     */
    public function collect($options=array())
    {
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
     * Return the mentions from twitter.
     * --------------------------------------------------
     * @param $count
     * @return TwitterDataCollector
     * --------------------------------------------------
     */
    private function getMentions($count)
    {
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
    protected static function isValidArticle($article)
    {
        if ( ! (array_key_exists('title', $article) &&
            array_key_exists('text', $article))) {
            return FALSe;
        }
        if (array_key_exists('hashtags', $article) && array_key_exists('created', $article) && array_key_exists('id', $article)) {
            return true;
        }
        return false;
    }

    /**
     * clearData
     * Delets all articles.
     */
    public function clearData()
    {
        $this->save(array());
    }

    /**
     * addArticle
     * Adds a new article to the dataset.
     * --------------------------------------------------
     * @param array $article
     * --------------------------------------------------
     */
    public function addArticle($article)
    {
        /* Checking article validity. */
        if ( ! static::isValidArticle($article)) {
            return;
        }
        /* Appending article to the current ones. */
        $articles = $this->getArticles();
        array_push($articles, $article);

        /* Saving data. */
        $this->save($articles);
    }

    /**
     * getArticles
     * Returns the articles from data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getArticles() {
        return $this->data;
    }
}
