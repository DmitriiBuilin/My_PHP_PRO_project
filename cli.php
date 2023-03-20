<?php

use GeekBrains\LevelTwo\Blog\Command\FakeData\PopulateDB;
use GeekBrains\LevelTwo\Blog\Command\Posts\DeletePost;
use GeekBrains\LevelTwo\Blog\Command\Users\CreateUser;
use GeekBrains\LevelTwo\Blog\Command\Users\UpdateUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$application = new Application();

$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

try {
    $application->run();
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}