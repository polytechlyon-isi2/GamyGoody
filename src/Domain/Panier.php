<?php

namespace GamyGoody\Domain;

class Pannier 
{
    private $libelleProduit;
    private $qteProduit;
    private $prixProduit;
    
    public function getLibelleProduit() {
        return $this->libelleProduit;
    }

    public function setLibelleProduit($libelleproduit) {
        $this->id = $libelleProduit;
    }
    
    public function getQteProduit() {
        return $this->qteProduit;
    }

    public function setQteProduit($qteProduit) {
        $this->id = $qteProduit;
    }
    
    public function getPrixProduit() {
        return $this->prixProduit;
    }

    public function setPrixProduit($prixProduit) {
        $this->id = $prixProduit;
    }
    
    
}