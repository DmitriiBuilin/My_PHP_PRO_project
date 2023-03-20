<?php


use GeekBrains\LevelTwo\Blog\Exceptions\AppException;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Http\Actions\Comments\CreateComment;
use GeekBrains\LevelTwo\Http\Actions\Comments\DeleteComment;
use GeekBrains\LevelTwo\Http\Actions\Likes\CreateLike;
use GeekBrains\LevelTwo\Http\Actions\Likes\ShowLikes;
use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Actions\Posts\DeletePost;
use GeekBrains\LevelTwo\Http\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\Http\Actions\Users\FindByUsername;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\Request;
use Psr\Log\LoggerInterface;
use GeekBrains\LevelTwo\Http\Actions\Auth\LogIn;
use GeekBrains\LevelTwo\Http\Actions\Auth\LogOut;

// require_once __DIR__ . '/vendor/autoload.php';   

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$request = new Request(
    $_GET, 
    $_SERVER, 
    file_get_contents('php://input'),
);

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/likes/show' => ShowLikes::class,
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/logout' => LogOut::class,
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/likes/create' => CreateLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
    ],   
];

try {
    $path = $request->path();
} catch (HttpException $e) {
    (new ErrorResponse)->send();
    $logger->warning($e->getMessage());
    return;
}

try {
// Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    // Логируем сообщение с уровнем NOTICE
        $message = "Route not found: $method $path";
        $logger->notice($message);
        (new ErrorResponse($message))->send();
        return;
    }

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
    $response->send();
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

