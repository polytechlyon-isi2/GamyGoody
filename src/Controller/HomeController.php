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


class HomeController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $games = $app['dao.game']->findAll();
        return $app['twig']->render('index.html.twig', array('games' => $games));
    }

    public function shopAction($game_id, Application $app)
    {
        $games = $app['dao.game']->findAll();
        if($app['dao.game']->isGameExistant($game_id))
        {
            $game = $app['dao.game']->find($game_id);
            $articles = $app['dao.article']->findAllByGameId($game_id);
        }
        else
        {
            $game = false;
            $articles = $app['dao.article']->findAll();
        }
        return $app['twig']->render('shop.html.twig', array('games' => $games, 'articles' => $articles, 'game' => $game));
    }

    public function articleAction($id, Request $request, Application $app)
    {
        $article = $app['dao.article']->find($id);

        $articlepanier = new ArticlePanier();
        $articlepanier->setArticle($article->getId());
        $articleForm = $app['form.factory']->create(new ArticlePanierType(), $articlepanier, ['action' =>  $app['url_generator']->generate('add_article_to_basket')]);
        $articleForm->handleRequest($request);
        $articleFormView = $articleForm->createView();

        $commentFormView = null;
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add comments
            $comment = new Comment();
            $comment->setArticle($article);
            $user = $app['user'];
            $comment->setAuthor($user);
            $commentForm = $app['form.factory']->create(new CommentType(), $comment);
            $commentForm->handleRequest($request);
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $app['dao.comment']->save($comment);
                $app['session']->getFlashBag()->add('success', 'Your comment was succesfully added.');
            }
            $commentFormView = $commentForm->createView();
        }
        $comments = $app['dao.comment']->findAllByArticle($id);
        return $app['twig']->render('article.html.twig', array(
            'article' => $article, 
            'comments' => $comments,
            'commentForm' => $commentFormView,
            'articleForm' => $articleFormView));
    }

    public function articlemodalAction($id, Request $request, Application $app)
    {
        $article = $app['dao.article']->find($id);

        $articlepanier = new ArticlePanier();
        $articlepanier->setArticle($article->getId());
        $articleForm = $app['form.factory']->create(new ArticlePanierType(), $articlepanier, ['action' =>  $app['url_generator']->generate('add_article_to_basket')]);
        $articleForm->handleRequest($request);
        $articleFormView = $articleForm->createView();

        return $app['twig']->render('article_modal.html.twig', array('article' => $article, 'articleForm' => $articleFormView));
    }

    public function loginAction(Request $request, Application $app) 
    {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            ));
    }

    public function registerAction(Request $request, Application $app) 
    {
        $user = new User();
        $userForm = $app['form.factory']->create(new UserRegisterType(), $user, array('app' => $app));
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid() && $user) {
        // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
        // find the default encoder
            $encoder = $app['security.encoder.digest'];
        // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);
            $user->setRole('ROLE_USER');
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Successfully registered.'.$user->getId());
        }
        return $app['twig']->render('user_register_form.html.twig', array(
            'title' => 'Register',
            'userForm' => $userForm->createView()));
    }

    public function profilAction(Application $app) 
    {
        $articles = $app['dao.article']->findAll();
        return $app['twig']->render('user_profil.html.twig');
    }

    public function profileeditAction(Request $request, Application $app) 
    {
        $user = $app['user'];
        $userForm = $app['form.factory']->create(new UserProfilType(), $user, array('app' => $app)); 
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $user->getPassword();
        // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
        // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Your profil was succesfully updated.');
        }
        return $app['twig']->render('user_profil_form.html.twig', array(
            'title' => 'Edit profil',
            'userForm' => $userForm->createView()));
    }

    public function addarticlepanierAction(Request $request, Application $app)
    {
        $articlepanier = new ArticlePanier();
        $user = $app['user'];
        $articleForm = $app['form.factory']->create(new ArticlePanierType(), $articlepanier, ['action' =>  $app['url_generator']->generate('add_article_to_basket')]);
        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()) 
        {
         if(!$app['session']->has('panier'))
         {
            $app['session']->set('panier', array($articlepanier->getArticle() => $articlepanier->getQuantity()));
         }
         else
         {
            $panier = $app['session']->get('panier');
            $panier[$articlepanier->getArticle()] = $articlepanier->getQuantity();
            $app['session']->set('panier', $panier);
         }

         $app['session']->set('panier_size', sizeof($app['session']->get('panier')));
     }

     return $app->redirect($app['url_generator']->generate('panier'));
 } 

 public function panierAction(Application $app)
     {
        $articles = $app['dao.panier']->buildAll();
        return $app['twig']->render('panier.html.twig', array('articles' => $articles));
    }
}
