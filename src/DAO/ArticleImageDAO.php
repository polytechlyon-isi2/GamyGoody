<?php

namespace GamyGoody\DAO;

use Doctrine\DBAL\Connection;
use GamyGoody\Domain\ArticleImage;

class ArticleImageDAO extends DAO
{
    private $imageDAO;
    private $articleDAO;

    public function setImageDAO($imageDAO){
        $this->imageDAO = $imageDAO;
    }

    protected function getImageDAO() {
        return $this->imageDAO;
    }

    public function setArticleDAO($articleDAO){
        $this->articleDAO = $articleDAO;
    }

    protected function getArticleDAO() {
        return $this->articleDAO;
    }

    public function findAllByArticle($id) {
        $sql = "select * from article_image where article_id=? order by level";
        $result = $this->getDb()->fetchAll($sql, array($id));

        // Convert query result to an array of domain objects
        $images = array();
        foreach ($result as $row) {
            $imageId = $row['image_id'];
            $images[] = $this->buildDomainObject($row);
        }
        return $images;
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \MicroCMS\Domain\Article
     */
    protected function buildDomainObject($row) {
        $articleimage = new ArticleImage();
        $articleimage->setId($row['id']);
        $articleimage->setLevel($row['level']);

        if (array_key_exists('image_id', $row)) {
            // Find and set the associated author
            $imgId = $row['image_id'];
            $image = $this->getImageDAO()->find($imgId);
            $articleimage->setImage($image);
        }

        return $articleimage;
    }

    public function find($id) {
        $sql = "select * from article_image where id=?";
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
    public function save(ArticleImage $articleimage) {
        $this->getImageDAO() -> save($articleimage -> getImage());
        $articleData = array(
            'article_id' => $articleimage->getArticle()->getId(),
            'image_id' => $articleimage->getImage()->getId(),
            'level' => $articleimage -> getLevel()
            );

        if ($articleimage->getId() != null) {
            // The article has already been saved : update it
            $this->getDb()->update('article_image', $articleData, array('id' => $articleimage->getId()));
        } else {
            // The article has never been saved : insert it
            $this->getDb()->insert('article_image', $articleData);
            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $articleimage->setId($id);
        }
    }

    /**
     * Removes an article from the database.
     *
     * @param integer $id The article id.
     */
    public function delete($id) {
        // Delete the article
        $image = $this->find($id)->getImage();
        $this->getDb()->delete('article', array('art_id' => $id));
        $this->getImageDAO()->deleteImage($image);
    }
    
     public function deleteAllByArticle($id) {
        $this->getDb()->delete('article_image', array('article_id' => $id));
    }
}