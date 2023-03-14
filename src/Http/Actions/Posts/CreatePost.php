<?php

namespace GeekBrains\LevelTwo\Http\Actions\Posts;

use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\PostRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\http\Actions\ActionInterface;
use GeekBrains\LevelTwo\http\ErrorResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\http\SuccessfulResponse;
use GeekBrains\LevelTwo\Http\Auth\IdentificationInterface;
use GeekBrains\LevelTwo\Http\Auth\JsonBodyUsernameIdentification;
use Psr\Log\LoggerInterface;
use GeekBrains\LevelTwo\Http\Auth\AuthenticationInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;


class CreatePost implements ActionInterface
{

    public function __construct(
        // private UsersRepositoryInterface $usersRepository,        
        private PostRepositoryInterface $postsRepository,
        private LoggerInterface $logger,
        private TokenAuthenticationInterface $authentication,
        // private IdentificationInterface $identification,
    )
    {
    }

    public function handle(Request $request): Response
    {
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
        // $user = $this->identification->user($request);

        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->save($post);
        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}