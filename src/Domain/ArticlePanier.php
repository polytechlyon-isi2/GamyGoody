<?php 

namespace GamyGoody\Domain;

class ArticlePanier 
{
    private $id;
    private $article;
    private $quantity;

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Gets the value of quantity.
     *
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets the value of quantity.
     *
     * @param mixed $quantity the quantity
     *
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }
}