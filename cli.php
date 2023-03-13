<?php

use GeekBrains\LevelTwo\Blog\Command\Arguments;
use GeekBrains\LevelTwo\Blog\Command\CreateUserCommand;
use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\CommentRepository\SqliteCommentRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\SqlitePostRepository;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

try {
    // При помощи контейнера создаём команду
    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));

} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}

// include __DIR__ . "/vendor/autoload.php";
// use GeekBrains\LevelTwo\Blog\UUID;

// //Создаём объект подключения к SQLite
// $connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

// $usersRepository = new SqliteUsersRepository($connection);
// $postRepository = new SqlitePostRepository($connection);
// $commentRepository = new SqliteCommentRepository($connection);

// try {
//     $user = $usersRepository->get(new UUID('f13e891e-c332-4ecf-b0c5-35e18802f958'));

//     $post = new Post(
//         UUID::random(),
//         $user,
//         "Header",
//         "Post text Lorem ipsum dolor"
//     );

//     $comment = new Comment(
//         UUID::random(),
//         $user,
//         $post,
//         "Any comment to 'Header' post."
//     );

//     // $commentRepository->save($comment);
//     // $post = $postRepository->get(new UUID("93f5956e-894c-4abа-8ce6-14e718cd5262"));
//     $comment = $commentRepository->get(new UUID("858cdf56-2775-46a3-bd0a-21a08cc85989"));

//     print_r($comment);


// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// $command = new CreateUserCommand($usersRepository);

// try {
//     $command->handle(Arguments::fromArgv($argv));
// } catch (Exception $e) {
//     echo $e->getMessage();
// }