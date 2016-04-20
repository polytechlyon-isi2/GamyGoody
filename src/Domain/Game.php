<?php

namespace GamyGoody\Domain;

class Game 
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
    private $title;

    /*
     * Game logo.
     *
     * @var png
     */
    private $logo;
    private $background;


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

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function getBackground()
    {
        return $this->background;
    }

    public function setBackground($background)
    {
        $this->background = $background;
    }
}