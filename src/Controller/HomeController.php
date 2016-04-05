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
use GamyGoody\Domain\ArticleImage;
use GamyGoody\Form\Type\CommentType;
use GamyGoody\Form\Type\ArticleType;
use GamyGoody\Form\Type\GameType;
use GamyGoody\Form\Type\CategoryType;
use GamyGoody\Form\Type\UserType;
use GamyGoody\Form\Type\UserRegisterType;
use GamyGoody\Form\Type\UserProfilType;
use GamyGoody\Form\Type\ArticleImageType;


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

    public function shopAction($game_id, $category_id, Application $app)
    {
        $games = $app['dao.game']->findAll();
        $categories = $app['dao.category']->findAll();
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
        return $app['twig']->render('shop.html.twig', array('games' => $games, 'categories' => $categories, 'articles' => $articles, 'game' => $game));
    }

    public function articleAction($id, Request $request, Application $app)
    {
        $article = $app['dao.article']->find($id);
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
            'commentForm' => $commentFormView));
    }

    public function articlemodalAction($id, Request $request, Application $app)
    {
        $article = $app['dao.article']->find($id);
        $bascketForm = null;
        return $app['twig']->render('article_modal.html.twig', array('article' => $article));
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
}
