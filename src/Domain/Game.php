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

    private $logo_dir;
    private $background_dir;

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

    public function setLogoByEx($ex)
    {
       $this->logo_dir = $this->getDir().'/logo_'.$this->getId().'.'.$ex;
    }

    public function setBackgroundByEx($ex)
    {
       $this->background_dir = $this->getDir().'/bg_'.$this->getId().'.'.$ex;
    }

    private function getDir()
    {
        return '/images/games';
    }

    public function getLogo_dir()
    {
        return $this->logo_dir;
    }

    public function getBackground_dir()
    {
        return $this->background_dir;
    }
}