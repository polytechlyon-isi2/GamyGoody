<?php

namespace GamyGoody\Domain;

class Category 
{
    /**
     * Category id.
     *
     * @var integer
     */
    private $id;

    /**
     * Category title.
     *
     * @var string
     */
    private $title;

    private $image;

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
}