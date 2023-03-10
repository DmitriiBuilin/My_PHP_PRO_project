<?php


use GeekBrains\LevelTwo\Blog\Exceptions\AppException;
use GeekBrains\LevelTwo\Blog\Repositories\CommentRepository\SqliteCommentRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\SqlitePostRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Http\Actions\Comments\CreateComment;
use GeekBrains\LevelTwo\Http\Actions\Comments\DeleteComment;
use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Actions\Posts\DeletePost;
use GeekBrains\LevelTwo\Http\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\Http\Actions\Users\FindByUsername;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET, 
    $_SERVER, 
    file_get_contents('php://input'),
);


$routes = [
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        
    ],
    'POST' => [ 
        '/users/create' => new CreateUser(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/create' => new CreatePost(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/comments/create' => new CreateComment(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteCommentRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        )
    ],
    'DELETE' => [ 
        '/posts' => new DeletePost(
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/comments' => new DeleteComment(
            new SqliteCommentRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        )
    ],
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
    (new ErrorResponse('Not found'))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
