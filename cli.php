<?php

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Person\{Name, Person};
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\InMemoryUsersRepository;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;

// spl_autoload_register('load');

include __DIR__ . "/vendor/autoload.php";

// function load($className)
// {
//     $file = $className . ".php"; 
//     $file = str_replace(["\\", "GeekBrains/LevelTwo"], ["/", "src"] , $file);
//     if (file_exists($file)) {
//         include $file;
//     }
// }

$name = new Name('Peter', 'Sidorov');

$user = new User(1, $name, "Admin");
echo $user;

$person = new Person($name, new DateTimeImmutable());


$post = new Post(
    1,
    $person,
    'Заголовок',
    'Всем привет!'
);

echo $post;

$name2 = new Name('Иван', 'Таранов');
$person2 = new Person($name2, new DateTimeImmutable());
$comment = new Comment(
    1,
    $person2,
    $post->id(),
    'Новый комментарий к первому посту'
);
echo $comment;

$user2 = new User(2, $name2, "User");
$userRepository = new InMemoryUsersRepository();
try {
$userRepository->save($user);
$userRepository->save($user2);

$faker = Faker\Factory::create('en_En');
$fakeName = $faker->name();
$fakeUser = new User(3, $fakeName, "Fake");
$fakePerson = new Person($fakeName, new DateTimeImmutable());
$fakePost = new Post(
    1,
    $fakePerson,
    'Заголовок к fakePost',
    'Привет мир!'
);

switch ($argv[1]) {
    case 'user':
        echo $fakeUser .PHP_EOL;
        $userRepository->save($fakeUser);
        break;
    case 'post':
        echo $fakePost .PHP_EOL;
        break;
    case 'comment':
        $fakeComment = new Comment(
            1,
            $fakePerson,
            $fakePost->id(),
            'Новый комментарий к fake посту'
        );
        echo $fakeComment .PHP_EOL;
        break;
}

    echo PHP_EOL;
    echo $userRepository->get(1);
    echo $userRepository->get(2);
    echo $userRepository->get(3);
} catch (UserNotFoundException | Exception $e) {
    echo $e->getMessage();
}