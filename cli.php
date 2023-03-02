<?php

use GeekBrains\LevelTwo\Blog\Command\Arguments;
use GeekBrains\LevelTwo\Blog\Command\CreateUserCommand;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\SqlitePostRepository;


include __DIR__ . "/vendor/autoload.php";
use GeekBrains\LevelTwo\Blog\UUID;

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postRepository = new SqlitePostRepository($connection);


try {
    $user = $usersRepository->get(new UUID('f13e891e-c332-4ecf-b0c5-35e18802f958'));

    $post = new Post(
        UUID::random(),
        $user,
        "Header",
        "Post text Lorem ipsum dolor"
    );

    // $postRepository->save($post);
    $post = $postRepository->get(new UUID("93f5956e-894c-4abd-8ce6-14e718cd5262"));

    print_r($post);
} catch (Exception $e) {
    echo $e->getMessage();
}

// $command = new CreateUserCommand($usersRepository);

// try {
//     $command->handle(Arguments::fromArgv($argv));
// } catch (Exception $e) {
//     echo $e->getMessage();
// }