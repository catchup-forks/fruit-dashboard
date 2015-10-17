<?php

/* This class is responsible for table widgets data collection. */
abstract class ArticleDataManager extends DataManager
{
    /**
     * initialize
     * Initializing the data.
     */
    public function initialize() {
        $this->collect();
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

    /**
     * getTitles
     * Returns all titles.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTitles() {
        $titles = array();
        foreach ($this->getArticles() as $article) {
            array_push($titles, $article['title']);
        }
        return $titles;
    }

    /**
     * clearData
     * Delets all articles.
     */
    public function clearData() {
        $this->save(array());
    }

    /**
     * addArticle
     * Adds a new article to the dataset.
     * --------------------------------------------------
     * @param array $article
     * --------------------------------------------------
     */
    public function addArticle($article) {
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
     * isValidArticle
     * Returns whether or not the article is valid.
     * --------------------------------------------------
     * @param array $article
     * @return boolean
     * --------------------------------------------------
     */
    protected static function isValidArticle($article) {
        if (array_key_exists('title', $article) && array_key_exists('text', $article)) {
            return TRUE;
        }
        return FALSE;
    }
}