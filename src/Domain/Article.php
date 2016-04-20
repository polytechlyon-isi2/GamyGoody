<?php

namespace GamyGoody\Domain;

class Article 
{
    /**
     * Article id.
     *
     * @var integer
     */
    private $id;

    /**
     * Article title.
     *
     * @var string
     */
    private $title;
    private $gameid;
    private $images;
    private $price;
    /**
     * Article content.
     *
     * @var string
     */
    private $content;

    public function __construct() {
        $images = array();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }
    
    public function getGame(){
        return $this->gameid;
    }
    
    public function setGame($id){
        $this->gameid = $id;
    }

    public function getPrice(){
        return $this->price;
    }
    
    public function setPrice(Image $price){
        $this->price = $price;
    }

    /**
     * Gets the value of images.
     *
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Sets the value of images.
     *
     * @param mixed $images the images
     *
     * @return self
     */
    public function setImages($images)
    {
        foreach ($images as $image) 
        {
            $image->setArticle($this);
        }
        $this->images = $images;

        return $this;
    }
}