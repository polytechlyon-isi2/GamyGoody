<?php

namespace GamyGoody\DAO;

use GamyGoody\Domain\Basket;

class BasketDAO
{
    public function creationBasket()    {
        if (!isset($_SESSION['basket'])){
            $_SESSION['basket']=array();
            $_SESSION['basket']['libelleProduit'] = array();
            $_SESSION['basket']['qteProduit'] = array();
            $_SESSION['basket']['prixProduit'] = array();
            $_SESSION['basket']['verrou'] = false;
         }
           return true;
    }
    
    public function addArticle($libelleProduit,$qteProduit,$prixProduit)
    {

       //Si le panier existe
       if (creationBasket() && !isVerrouille())
       {
          //Si le produit existe déjà on ajoute seulement la quantité
          $positionProduit = array_search($libelleProduit,  $_SESSION['basket']['libelleProduit']);

          if ($positionProduit !== false)
          {
             $_SESSION['basket']['qteProduit'][$positionProduit] += $qteProduit ;
          }
          else
          {
             //Sinon on ajoute le produit
             array_push( $_SESSION['basket']['libelleProduit'],$libelleProduit);
             array_push( $_SESSION['basket']['qteProduit'],$qteProduit);
             array_push( $_SESSION['basket']['prixProduit'],$prixProduit);
          }
       }
       else
       echo "Un problème est survenu veuillez contacter l'administrateur du site.";
    }
    
    public function supArticle($libelleProduit)
    {
       //Si le panier existe
       if (creationBasket() && !isVerrouille())
       {
          //Nous allons passer par un panier temporaire
          $tmp=array();
          $tmp['libelleProduit'] = array();
          $tmp['qteProduit'] = array();
          $tmp['prixProduit'] = array();
          $tmp['verrou'] = $_SESSION['basket']['verrou'];

          for($i = 0; $i < count($_SESSION['basket']['libelleProduit']); $i++)
          {
             if ($_SESSION['basket']['libelleProduit'][$i] !== $libelleProduit)
             {
                array_push( $tmp['libelleProduit'],$_SESSION['basket']['libelleProduit'][$i]);
                array_push( $tmp['qteProduit'],$_SESSION['basket']['qteProduit'][$i]);
                array_push( $tmp['prixProduit'],$_SESSION['basket']['prixProduit'][$i]);
             }

          }
          //On remplace le panier en session par notre panier temporaire à jour
          $_SESSION['basket'] =  $tmp;
          //On efface notre panier temporaire
          unset($tmp);
       }
       else
       echo "Un problème est survenu veuillez contacter l'administrateur du site.";
    }
    
    public function modifQTeArticle($libelleProduit,$qteProduit)
    {
       //Si le panier éxiste
       if (creationBasket() && !isVerrouille())
       {
          //Si la quantité est positive on modifie sinon on supprime l'article
          if ($qteProduit > 0)
          {
             //Recharche du produit dans le panier
             $positionProduit = array_search($libelleProduit,  $_SESSION['basket']['libelleProduit']);

             if ($positionProduit !== false)
             {
                $_SESSION['basket']['qteProduit'][$positionProduit] = $qteProduit ;
             }
          }
          else
          supArticle($libelleProduit);
       }
       else
       echo "Un problème est survenu veuillez contacter l'administrateur du site.";
    }
    
    public function MontantGlobal()
    {
       $total=0;
       for($i = 0; $i < count($_SESSION['basket']['libelleProduit']); $i++)
       {
          $total += $_SESSION['basket']['qteProduit'][$i] * $_SESSION['basket']['prixProduit'][$i];
       }
       return $total;
    }
    
}