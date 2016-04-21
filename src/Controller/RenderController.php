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


class RenderController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function navbarAction(Application $app) {
        $games = $app['dao.game']->findAll();
        return $app['twig']->render('navbar.html.twig', ['games' => $games]);
    }
}