<?php

namespace GeekBrains\LevelTwo\Http\Actions\Comments;

use GeekBrains\LevelTwo\Blog\Exceptions\CommentsNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use Psr\Log\LoggerInterface;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentsRepository,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        $container = require 'bootstrap.php';
        $logger = $container->get(LoggerInterface::class);

        try {
            $commentUuid = $request->query('uuid');
            $this->commentsRepository->get(new UUID($commentUuid));

        } catch (CommentsNotFoundException $error) {
            $logger->warning($error->getMessage());
            return new ErrorResponse($error->getMessage());
        }

        $this->commentsRepository->delete(new UUID($commentUuid));
        $logger->info("Comment deleted: $commentUuid");

        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}