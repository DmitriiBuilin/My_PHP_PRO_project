<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\LikeRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\LikeAlreadyExist;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeNotFoundExeption;
use GeekBrains\LevelTwo\Blog\Like;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\SqlitePostRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use \PDO;
use \PDOStatement;

class SqliteLikeRepository implements LikeRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
        'INSERT INTO likes (uuid, post_uuid, author_uuid) 
            VALUES (:uuid, :post_uuid, :author_uuid)'
        );

        $statement->execute([
            ':uuid' => $like->getUuid(),
            ':post_uuid' => $like->getPost()->getUuid(),
            ':author_uuid' => $like->getUser()->uuid(),            
        ]);
    }


    public function getByPostUuid(UUID $postUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid'
        );

        $statement->execute([':post_uuid' => (string)$postUuid]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new LikeNotFoundException(
                'No likes to post with uuid = : ' . $postUuid
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection);


        $postRepository = new SqlitePostRepository($this->connection);


        $likes = [];
        foreach ($result as $like) {
            print_r($like);
            $user = $userRepository->get(new UUID($like['author_uuid']));
            $post = $postRepository->get(new UUID($like['post_uuid']));
            $likes[] = new Like(
                uuid: new UUID($like['uuid']),
                post: $post,
                user: $user,
            );
        }

        return $likes;
    }

    // public function checkUserLikeForPostExists($postUuid, $userUuid): void
    // {
    //     $statement = $this->connection->prepare(
    //         'SELECT *
    //         FROM likes
    //         WHERE 
    //             post_uuid = :postUuid AND user_uuid = :userUuid'
    //     );

    //     $statement->execute(
    //         [
    //             ':postUuid' => $postUuid,
    //             ':userUuid' => $userUuid
    //         ]
    //     );

    //     $isExisted = $statement->fetch();

    //     if ($isExisted) {
    //         throw new LikeAlreadyExist(
    //             'The users like for this post already exists'
    //         );
    //     }
    // }


    // private function getLike(PDOStatement $statement, string $postUuid): Like
    // {
    //     $result = $statement->fetch(PDO::FETCH_ASSOC);
    //     if ($result === false) {
    //         throw new LikeNotFoundException(
    //             "Cannot find like from post: $postUuid"
    //         );
    //     }

    //     $userRepository = new SqliteUsersRepository($this->connection);
    //     $user = $userRepository->get(new UUID($result['author_uuid']));

    //     $postRepository = new SqlitePostRepository($this->connection);
    //     $post = $postRepository->get(new UUID($result['post_uuid']));

    //     return new Like(
    //         new UUID($result['uuid']),
    //         $post,
    //         $user,
    //     );
    // }	
}