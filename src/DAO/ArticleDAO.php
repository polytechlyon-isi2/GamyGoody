<?php

namespace GamyGoody\DAO;

use GamyGoody\Domain\Article;

class ArticleDAO extends DAO
{
    private $imageDAO;
    
    public function setImageDAO($imageDAO){
        $this->imageDAO = $imageDAO;
    }
    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select * from article order by art_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

        /**
     * Return a list of articles, sorted by date (most recent first) and game Id.
     *
     * @param int $id id of the game.
     * @return array A list of articles.
     */
    public function findAllByGameId($id) {
        $sql = "select * from article where game_id=? order by game_id desc";
        $result = $this->getDb()->fetchAll($sql, array($id));

        // Convert query result to an array of domain objects
        $games = array();
        foreach ($result as $row) {
            $gameId = $row['game_id'];
            $games[$gameId] = $this->buildDomainObject($row);
        }
        return $games;
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \MicroCMS\Domain\Article
     */
    protected function buildDomainObject($row) {
        $article = new Article();
        $article->setId($row['art_id']);
        $article->setTitle($row['art_title']);
        $article->setContent($row['art_content']);
        $article->setCategory($row['cat_id']);
        $article->setGame($row['game_id']);
        $article->setImage($imageDAO -> buildDomainObject($row));
        return $article;
    }

    public function find($id) {
        $sql = "select * from article where art_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $id);
    }

    /**
     * Saves an article into the database.
     *
     * @param \MicroCMS\Domain\Article $article The article to save
     */
    public function save(Article $article) {
        $imageDAO -> save($article -> getImage());
        $articleData = array(
            'art_title' => $article->getTitle(),
            'art_content' => $article->getContent(),
            'cat_id' => $article -> getCategory(),
            'game_id' => $article -> getGame(),
            );

        if ($article->getId()) {
            // The article has already been saved : update it
            $this->getDb()->update('article', $articleData, array('art_id' => $article->getId()));
        } else {
            // The article has never been saved : insert it
            $this->getDb()->insert('article', $articleData);
            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $article->setId($id);
        }
    }

    /**
     * Removes an article from the database.
     *
     * @param integer $id The article id.
     */
    public function delete($id) {
        // Delete the article
        $this->getDb()->delete('article', array('art_id' => $id));
    }
    
     public function deleteAllByGame($id) {
        $this->getDb()->delete('article', array('game_id' => $id));
    }
}