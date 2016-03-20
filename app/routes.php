<?php

use Symfony\Component\HttpFoundation\Request;
use GamyGoody\Domain\Comment;
use GamyGoody\Domain\Article;
use GamyGoody\Domain\User;
use  GamyGoody\Domain\Game;
use GamyGoody\Form\Type\CommentType;
use GamyGoody\Form\Type\ArticleType;
use  GamyGoody\Form\Type\GameType;
use GamyGoody\Form\Type\UserType;
use GamyGoody\Form\Type\UserRegisterType;
use GamyGoody\Form\Type\UserProfilType;

// Home page
$app->get('/', function () use ($app) {
    $games = $app['dao.game']->findAll();
    return $app['twig']->render('index.html.twig', array('games' => $games));
})->bind('home');

// Shop page with all articles filtered by game, category, 
$app->get('/shop/{game_id}/{category_id}', function ($game_id, $category_id) use ($app) 
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
})->value('game_id', '')->value('category_id', '')->bind('shop');

// Article details with comments
$app->match('/article/{id}', function ($id, Request $request) use ($app) {
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
})->bind('article');

// Login form
$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
        ));
})->bind('login');

// Register form
$app->match('/register', function(Request $request) use ($app) {
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
        $app['session']->getFlashBag()->add('success', 'Successfully registered.');
    }
    return $app['twig']->render('user_register_form.html.twig', array(
        'title' => 'Register',
        'userForm' => $userForm->createView()));
})->bind('register');

// Profil page
$app->get('/profil', function () use ($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('user_profil.html.twig');
})->bind('profil');

// Edit profil
$app->match('/profil/edit', function (Request $request) use ($app) {
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
})->bind('profil_edit');


// Admin home page
$app->get('/admin', function() use ($app) {
    $articles = $app['dao.article']->findAll();
    $categories = $app['dao.category']->findAll();
    $comments = $app['dao.comment']->findAll();
    $users = $app['dao.user']->findAll();
    $games = $app['dao.game']->findAll();
    return $app['twig']->render('admin.html.twig', array(
        'articles' => $articles,
        'comments' => $comments,
        'games' => $games,
        'users'    => $users,
        'categories' => $categories));
})->bind('admin');

// Add a new article
$app->match('/admin/article/add', function(Request $request) use ($app) {
    $article = new Article();
    $games = $app['dao.game'] ->findAllTitles();
    $categories = $app['dao.category'] ->findAllTitles();
    $articleForm = $app['form.factory']->create(new ArticleType(), $article, array('games' => $games, 'categories' => $categories));
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'The article was successfully created.');
    }
    return $app['twig']->render('article_form.html.twig', array(
        'title'       => 'New article',
        'articleForm' => $articleForm->createView()));
})->bind('admin_article_add');

// Edit an existing article
$app->match('/admin/article/{id}/edit', function($id, Request $request) use ($app) {
    $article = $app['dao.article']->find($id);
    $games = $app['dao.game'] ->findAllTitles();
    $categories = $app['dao.category'] ->findAllTitles();
    $articleForm = $app['form.factory']->create(new ArticleType(), $article, array('games' => $games, 'categories' => $categories));
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'The article was succesfully updated.');
    }
    return $app['twig']->render('article_form.html.twig', array(
        'title' => 'Edit article',
        'articleForm' => $articleForm->createView()));
})->bind('admin_article_edit');

// Remove an article
$app->get('/admin/article/{id}/delete', function($id, Request $request) use ($app) {
    // Delete all associated comments
    $app['dao.comment']->deleteAllByArticle($id);
    // Delete the article
    $app['dao.article']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The article was succesfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_article_delete');

// Edit an existing comment
$app->match('/admin/comment/{id}/edit', function($id, Request $request) use ($app) {
    $comment = $app['dao.comment']->find($id);
    $commentForm = $app['form.factory']->create(new CommentType(), $comment);
    $commentForm->handleRequest($request);
    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $app['dao.comment']->save($comment);
        $app['session']->getFlashBag()->add('success', 'The comment was succesfully updated.');
    }
    return $app['twig']->render('comment_form.html.twig', array(
        'title' => 'Edit comment',
        'commentForm' => $commentForm->createView()));
})->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/comment/{id}/delete', function($id, Request $request) use ($app) {
    $app['dao.comment']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The comment was succesfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_comment_delete');

// Add a user
$app->match('/admin/user/add', function(Request $request) use ($app) {
    $user = new User();
    $userForm = $app['form.factory']->create(new UserType(), $user, array('app' => $app));
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        // generate a random salt value
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();
        // find the default encoder
        $encoder = $app['security.encoder.digest'];
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password); 
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
    }
    return $app['twig']->render('user_form.html.twig', array(
        'title' => 'New user',
        'userForm' => $userForm->createView()));
})->bind('admin_user_add');

// Edit an existing user
$app->match('/admin/user/{id}/edit', function($id, Request $request) use ($app) {
    $user = $app['dao.user']->find($id);
    $userForm = $app['form.factory']->create(new UserType(), $user, array('app' => $app));
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        $plainPassword = $user->getPassword();
        // find the encoder for the user
        $encoder = $app['security.encoder_factory']->getEncoder($user);
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password); 
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was succesfully updated.');
    }
    return $app['twig']->render('user_form.html.twig', array(
        'title' => 'Edit user',
        'userForm' => $userForm->createView()));
})->bind('admin_user_edit');

// Remove a user
$app->get('/admin/user/{id}/delete', function($id, Request $request) use ($app) {
    // Delete all associated comments
    $app['dao.comment']->deleteAllByUser($id);
    // Delete the user
    $app['dao.user']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The user was succesfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_user_delete');

// Add a new game
$app->match('/admin/game/add', function(Request $request) use ($app) {
    $game = new Game();
    $gameForm = $app['form.factory']->create(new GameType(), $game);
    $gameForm->handleRequest($request);
    if ($gameForm->isSubmitted() && $gameForm->isValid()) {
        $app['dao.game']->save($game);
        $app['session']->getFlashBag()->add('success', 'The game was successfully created.');
    }
    return $app['twig']->render('game_form.html.twig', array(
        'title'       => 'New game',
        'gameForm' => $gameForm->createView()));
})->bind('admin_game_add');

// Edit an existing game
$app->match('/admin/game/{id}/edit', function($id, Request $request) use ($app) {
    $game = $app['dao.game']->find($id);
    $gameForm = $app['form.factory']->create(new GameType(), $game);
    $gameForm->handleRequest($request);
    if ($gameForm->isSubmitted() && $gameForm->isValid()) {
        $app['dao.game']->save($game);
        $app['session']->getFlashBag()->add('success', 'The game was succesfully updated.');
    }
    return $app['twig']->render('game_form.html.twig', array(
        'title' => 'Edit game',
        'gameForm' => $gameForm->createView()));
})->bind('admin_game_edit');

// Remove an article
$app->get('/admin/game/{id}/delete', function($id, Request $request) use ($app) {
    // Delete all associated comments
    $app['dao.article']->deleteAllByGame($id);
    // Delete the article
    $app['dao.game']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The article was succesfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_game_delete');
