<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\CommentRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\SqlitePostRepository;
use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\UUID;
use \PDO;
use \PDOStatement;

class SqliteCommentRepository implements CommentRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
        'INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post_uuid' => $comment->getPost()->getUuid(),
            ':author_uuid' => $comment->getUser()->uuid(),
            ':text' => $comment->getText(),
        ]);
    }


    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([':uuid' => (string)$uuid]);

        return $this->getComment($statement, $uuid);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    private function getComment(PDOStatement $statement, string $commentUuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find comment: $commentUuid"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        $postRepository = new SqlitePostRepository($this->connection);
        $post = $postRepository->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['text']
        );
    }
	/**
	 * @param UUID $uuid
	 */
	public function delete(UUID $uuid): void {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE comments.uuid=:uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);
	}
}