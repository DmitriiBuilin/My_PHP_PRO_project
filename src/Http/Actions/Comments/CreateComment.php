<?php

namespace GeekBrains\LevelTwo\Http\Actions\Comments;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\PostRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\http\Actions\ActionInterface;
use GeekBrains\LevelTwo\http\ErrorResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentRepositoryInterface $commentsRepository,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException| InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException| InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $newCommentUuid = UUID::random();
        
        try {
            $comment = new Comment(
                $newCommentUuid,
                $user,
                $post,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->commentsRepository->save($comment);
        $this->logger->info("Comment created: $newCommentUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}