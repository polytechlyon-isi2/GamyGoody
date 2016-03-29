<?php

namespace GamyGoody\Domain;

class ArticleImage
{
    /**
     * Game id.
     *
     * @var integer
     */
    private $id;

    /**
     * Game title.
     *
     * @var string
     */
    private $image;

    private $article;

    private $level = 0;


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getImage() {
        return $this->id;
    }

    public function setImage($id) {
        $this->id = $id;
    }

    /**
     * Gets the value of article.
     *
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Sets the value of article.
     *
     * @param mixed $article the article
     *
     * @return self
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Gets the value of level.
     *
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the value of level.
     *
     * @param mixed $level the level
     *
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }
}