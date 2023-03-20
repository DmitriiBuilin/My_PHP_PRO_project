<?php

namespace GeekBrains\LevelTwo\Blog\Command\FakeData;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\PostRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use Symfony\Component\Console\Command\Command;
use Faker\Generator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentRepositoryInterface $commentsRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-quantity',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Users quantity'
            )
            ->addOption(
                'posts-quantity',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Posts quantity'
            )
            ->addOption(
                'comments-quantity',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Comments quantity'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        // Получаем значения опций
        $usersQuantity = $input->getOption('users-quantity');
        $postsQuantity = $input->getOption('posts-quantity');
        $commentsQuantity= $input->getOption('comments-quantity');

        // Создаём пользователей
        $users = [];
        for ($i = 0; $i < $usersQuantity; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }

        // От имени каждого пользователя создаём статьи
        foreach ($users as $user) {
            for ($i = 0; $i < $postsQuantity; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }

        // Под каждым постом создаём комментарии
        foreach ($posts as $post) {
            for ($i = 0; $i < $commentsQuantity; $i++) {
                $comment = $this->createFakeComment($post, $user);
                $output->writeln('Comment created: ' . $comment->getText());
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
        // Генерируем имя пользователя
            $this->faker->userName,
            // Генерируем пароль
            $this->faker->password,
            new Name(
            // Генерируем имя
                $this->faker->firstName,
                // Генерируем фамилию
                $this->faker->lastName
            )
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(Post $post, User $author): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author,
            $post,
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->commentsRepository->save($comment);
        return $comment;
    }
}