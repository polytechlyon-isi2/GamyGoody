<?php

namespace GamyGoody\Domain;

class Image 
{
    /**
     * Comment id.
     *
     * @var integer
     */
    private $id;

    /**
     * Comment author.
     *
     * @var string
     */
    private $url;
    /**
     * Comment content.
     *
     * @var integer
     */
    private $alt;

    /**
     * Associated article.
     *
     * @var \MicroCMS\Domain\Article
     */
    private $file;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getAlt() {
        return $this->alt;
    }

    public function setAlt($alt) {
        $this->alt = $alt;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
    }
}