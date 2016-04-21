<?php

namespace GamyGoody\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use GamyGoody\Domain\Comment;
use GamyGoody\Domain\Article;
use GamyGoody\Domain\User;
use GamyGoody\Domain\Game;
use GamyGoody\Domain\Category;
use GamyGoody\Domain\Image;
use GamyGoody\Domain\ArticlePanier;
use GamyGoody\Domain\ArticleImage;
use GamyGoody\Form\Type\CommentType;
use GamyGoody\Form\Type\ArticleType;
use GamyGoody\Form\Type\GameType;
use GamyGoody\Form\Type\CategoryType;
use GamyGoody\Form\Type\UserType;
use GamyGoody\Form\Type\UserRegisterType;
use GamyGoody\Form\Type\UserProfilType;
use GamyGoody\Form\Type\ArticleImageType;
use GamyGoody\Form\Type\ArticlePanierType;


class PanierController {


    public function panierAction(Application $app)
    {
        $articles = $app['dao.panier']->buildAll();
        return $app['twig']->render('panier.html.twig', array('articles' => $articles));
    }

    public function trashAction(Application $app)
    {
        $app['dao.panier']->clear();
        return $app->redirect($app['url_generator']->generate('home'));
    }

    public function addarticlepanierAction(Request $request, Application $app)
    {
        $articlepanier = new ArticlePanier();
        $articleForm = $app['form.factory']->create(new ArticlePanierType(), $articlepanier, ['action' =>  $app['url_generator']->generate('add_article_to_basket')]);
        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()) 
        {
           $app['dao.panier']->addArticle($articlepanier);
        }

        return $app->redirect($app['url_generator']->generate('panier'));
    } 
}