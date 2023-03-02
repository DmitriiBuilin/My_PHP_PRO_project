<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use \PDO;
use \PDOStatement;

class SqlitePostRepository implements PostRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
        'INSERT INTO posts (uuid, author_uuid, title, text) 
            VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => $post->getUuid(),
            ':author_uuid' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);
    }


    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([':uuid' => (string)$uuid]);


        // $result = $statement->fetch(PDO::FETCH_ASSOC);

        // // Бросаем исключение, если пользователь не найден
        // if ($result === false) {
        //     throw new UserNotFoundException(
        //         "Cannot get user: $uuid"
        //     );
        // }
        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    private function getPost(\PDOStatement $statement, string $postUuid): Post
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $postUuid"
            );
        }

        // print_r($result);
        $userRepository = new SqliteUsersRepository($this->connection);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }

    // /**
    //  * @throws UserNotFoundException
    //  * @throws InvalidArgumentException
    //  */
    // public function getByUsername(string $username): User
    // {
    //     $statement = $this->connection->prepare(
    //         'SELECT * FROM users WHERE username = :username'
    //     );
    //     $statement->execute([
    //         ':username' => $username,
    //     ]);

    //    return $this->getUser($statement, $username);
    // }


}