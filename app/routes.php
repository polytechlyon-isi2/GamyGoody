<?php

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

// Home page
$app->get('/', "GamyGoody\Controller\HomeController::indexAction")->bind('home');
// Shop page with all articles filtered by game, category, 
$app->get('/shop/{game_id}/{category_id}', "GamyGoody\Controller\HomeController::shopAction")->value('game_id', '')->value('category_id', '')->bind('shop');

// Article details with comments
$app->match('/article/{id}', "GamyGoody\Controller\HomeController::articleAction")->bind('article');


// Article modal
$app->match('/article/modal/{id}', "GamyGoody\Controller\HomeController::articlemodalAction")->bind('article_modal');


// Login form
$app->get('/login', "GamyGoody\Controller\HomeController::loginAction")->bind('login');

// Register form
$app->match('/register', "GamyGoody\Controller\HomeController::registerAction")->bind('register');

// Profil page
$app->get('/profil', "GamyGoody\Controller\HomeController::profilAction")->bind('profil');

// Edit profil
$app->match('/profil/edit', "GamyGoody\Controller\HomeController::profileditAction")->bind('profil_edit');


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

// Basket home page
$app->get('/basket', function () use ($app) {
    $basket = $app['dao.basket']->creationBasket();
    return $app['twig']->render('basket.html.twig');
})->bind('basket');

// Add a new article
$app->match('/admin/article/add', function(Request $request) use ($app) {
    $article = new Article();
    $article->setImages([new ArticleImage()]);
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


// Remove an game
$app->get('/admin/game/{id}/delete', function($id, Request $request) use ($app) {
    // Delete all associated comments
    $app['dao.article']->deleteAllByGame($id);
    // Delete the article
    $app['dao.game']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The article was succesfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_game_delete');

// Add a new category
$app->match('/admin/category/add', function(Request $request) use ($app) {
    $category = new Category();
    $categoryForm = $app['form.factory']->create(new CategoryType(), $category);
    $categoryForm->handleRequest($request);
    if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
        $app['dao.category']->save($category);
        $app['session']->getFlashBag()->add('success', 'The category was successfully created.');
    }
    return $app['twig']->render('category_form.html.twig', array(
        'title' => 'New category',
        'categoryForm' => $categoryForm->createView()));
})->bind('admin_category_add');

// Edit an existing category
$app->match('/admin/category/{id}/edit', function($id, Request $request) use ($app) {
    $category = $app['dao.category']->find($id);
    $categoryForm = $app['form.factory']->create(new CategoryType(), $category);
    $categoryForm->handleRequest($request);
    if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
        $app['dao.category']->save($category);
        $app['session']->getFlashBag()->add('success', 'The category was succesfully updated.');
    }
    return $app['twig']->render('category_form.html.twig', array(
        'title' => 'Edit category',
        'categoryForm' => $categoryForm->createView()));
})->bind('admin_category_edit');

// Remove an category
$app->get('/admin/category/{id}/delete', function($id, Request $request) use ($app) {
    // Delete all associated comments
    $app['dao.category']->deleteAllByCategory($id);
    // Delete the category
    $app['dao.category']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The category was succesfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_category_delete');

