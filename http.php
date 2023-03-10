<?php


use GeekBrains\LevelTwo\Blog\Exceptions\AppException;

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

// require_once __DIR__ . '/vendor/autoload.php';   

$container = require __DIR__ . '/bootstrap.php';

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
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/likes/create' => CreateLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
    ],
    // 'GET' => [
    //     '/users/show' => new FindByUsername(
    //         new SqliteUsersRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         )
    //     ),
        
    // ],
    // 'POST' => [ 
    //     '/users/create' => new CreateUser(
    //         new SqliteUsersRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         )
    //     ),
    //     '/posts/create' => new CreatePost(
    //         new SqliteUsersRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         ),
    //         new SqlitePostRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         )
    //     ),
    //     '/comments/create' => new CreateComment(
    //         new SqliteUsersRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         ),
    //         new SqlitePostRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         ),
    //         new SqliteCommentRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         )
    //     )
    // ],
    // 'DELETE' => [ 
    //     '/posts' => new DeletePost(
    //         new SqlitePostRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         )
    //     ),
    //     '/comments' => new DeleteComment(
    //         new SqliteCommentRepository(
    //             new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //         )
    //     )
    // ],
];


try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
// Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
// Возвращаем неудачный ответ,
// если по какой-то причине
// не можем получить метод
    (new ErrorResponse)->send();
    return;
}

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
    $response->send();
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();