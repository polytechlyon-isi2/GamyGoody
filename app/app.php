<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;


// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\HttpFragmentServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app['twig'] = $app->share($app->extend('twig', function(Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
}));
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users' => $app->share(function () use ($app) {
                return new GamyGoody\DAO\UserDAO($app['db']);
            }),
        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
    'security.access_rules' => array(
        array('^/admin', 'ROLE_ADMIN'),
    ),
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());

$app['dao.image'] = $app->share(function ($app) {
    return new GamyGoody\DAO\ImageDAO($app['db']);
});

$app['dao.article_image'] = $app->share(function ($app) {
    $article_imageDAO = new GamyGoody\DAO\ArticleImageDAO($app['db']);
    $article_imageDAO -> setImageDAO($app['dao.image']);
    return $article_imageDAO;
});

$app['dao.user'] = $app->share(function ($app) {
    return new GamyGoody\DAO\UserDAO($app['db']);
});

$app['dao.basket'] = $app->share(function ($app) {
    return new GamyGoody\DAO\BasketDAO($app['db']);
});

$app['dao.game'] = $app->share(function ($app) {
    $gameDAO = new GamyGoody\DAO\GameDAO($app['db']);
    $gameDAO -> setImageDAO($app['dao.image']);
    return $gameDAO;
});
$app['dao.category'] = $app->share(function ($app) {
    $catDAO = new GamyGoody\DAO\CategoryDAO($app['db']);
    //$catDAO -> setImageDAO($app['dao.image']);
    return $catDAO;
});
// Register services
$app['dao.article'] = $app->share(function ($app) {
    $articleDAO = new GamyGoody\DAO\ArticleDAO($app['db']);
    $articleDAO -> setImageDAO($app['dao.image']);
    $articleDAO -> setGameDAO($app['dao.game']);
    $articleDAO -> setArticleImageDAO($app['dao.article_image']);
    return $articleDAO;
});

$app['dao.comment'] = $app->share(function ($app) {
    $commentDAO = new GamyGoody\DAO\CommentDAO($app['db']);
    $commentDAO->setArticleDAO($app['dao.article']);
    $commentDAO->setUserDAO($app['dao.user']);
    return $commentDAO;
});

$app['dao.panier'] = $app->share(function ($app) {

    $panierDAO = new GamyGoody\DAO\PanierDAO($app['session']);
    $panierDAO->setArticleDAO($app['dao.article']);
    return $panierDAO;
});