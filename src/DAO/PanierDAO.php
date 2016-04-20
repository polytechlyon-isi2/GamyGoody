<?php

namespace GamyGoody\DAO;

use Symfony\Component\HttpFoundation\Session\Session;
use GamyGoody\Domain\Panier;
use GamyGoody\Domain\ArticlePanier;

class PanierDAO
{
    private $session;

    private $articleDAO;

    public function setArticleDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }

    public function __construct(Session $session) {
        $this->session = $session;
    }

    public function buildAll()
    {
        if(!$this->session->has('panier'))
        {
            $this->session->set('panier', []);
            $this->session->set('panier_size', 0);
        }
        $panier_ar = $this->session->get('panier');
        $panier = [];
        foreach ($panier_ar as $key => $value) {
            //echo "Clé : $key; Valeur : $value<br />\n";
            $articlepanier = new ArticlePanier();
            $articlepanier->setQuantity($value);
            $articlepanier->setArticle($this->articleDAO->find($key));
            $panier[] = $articlepanier;
        }
        return $panier;
    }

    public function save()
    {
        $panier_ar = $this->session->get('panier');
        $panier = [];
        foreach ($panier_ar as $key => $value) {
            //echo "Clé : $key; Valeur : $value<br />\n";
            $articlepanier = new ArticlePanier();
            $articlepanier->setQuantity($value);
            $panier[] = $articlepanier;
        }
        return $panier;
    }
}