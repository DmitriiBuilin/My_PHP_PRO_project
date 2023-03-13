<?php

namespace GeekBrains\LevelTwo\Http\Actions\Posts;

use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\PostRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use Psr\Log\LoggerInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private LoggerInterface $logger,
    )
    {
    }


    public function handle(Request $request): Response
    {
        $container = require 'bootstrap.php';
        $logger = $container->get(LoggerInterface::class);

        try {
            $postUuid = $request->query('uuid');
            $this->postsRepository->get(new UUID($postUuid));

        } catch (PostNotFoundException $error) {
            $logger->warning($error->getMessage());
            return new ErrorResponse($error->getMessage());
        }

        $this->postsRepository->delete(new UUID($postUuid));
        $logger->info("Post deleted: $postUuid");

        return new SuccessfulResponse([
            'uuid' => $postUuid,
        ]);
    }
}