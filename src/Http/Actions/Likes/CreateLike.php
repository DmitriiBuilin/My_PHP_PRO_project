<?php

namespace GeekBrains\LevelTwo\Http\Actions\Likes;

use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeAlreadyExist;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Like;
use GeekBrains\LevelTwo\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\PostRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use GeekBrains\LevelTwo\http\ErrorResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateLike implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private LikeRepositoryInterface $likeRepository,
        private LoggerInterface $logger,
        private TokenAuthenticationInterface $authentication,
    )
    {
    }

    public function handle(Request $request): Response
    {
        $container = require 'bootstrap.php';
        $logger = $container->get(LoggerInterface::class);

        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            $logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException| InvalidArgumentException $exception) {
            $logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $exception) {
            $logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        // try {
        //     $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        // } catch (HttpException| InvalidArgumentException $exception) {
        //     return new ErrorResponse($exception->getMessage());
        // }

        // try {
        //     $user = $this->usersRepository->get($authorUuid);
        // } catch (UserNotFoundException $exception) {
        //     return new ErrorResponse($exception->getMessage());
        // }

        // try {
        //     $this->likeRepository->checkUserLikeForPostExists($postUuid, $authorUuid);
        // } catch (LikeAlreadyExist $e) {
        //     return new ErrorResponse($e->getMessage());
        // }
        
        $newLikeUuid = UUID::random();
        
        try {
            $like = new Like(
                $newLikeUuid,
                $post,
                $user,                
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->likeRepository->save($like);
        $this->logger->info("Like created: $newLikeUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}