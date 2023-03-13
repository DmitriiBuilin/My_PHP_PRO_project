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

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->query('uuid');
            $this->commentsRepository->get(new UUID($commentUuid));

        } catch (CommentsNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->commentsRepository->delete(new UUID($commentUuid));

        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}