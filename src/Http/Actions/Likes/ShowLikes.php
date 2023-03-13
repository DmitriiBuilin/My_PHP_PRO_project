<?php

namespace GeekBrains\LevelTwo\Http\Actions\Likes;

use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\PostRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;


/**
 * Summary of ShowLike
 */
class ShowLikes implements ActionInterface
{
    /**
     * Summary of __construct
     * @param PostRepositoryInterface $postsRepository
     * @param LikeRepositoryInterface $usersRepository
     */
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private LikeRepositoryInterface $likeRepository
    )
    {
    }

 /**
  * Summary of handle
  * @param \GeekBrains\LevelTwo\http\Request $request
  * @return \GeekBrains\LevelTwo\http\Response
  */
	public function handle(Request $request): Response 
    {
        try {
            $postUuid = new UUID($request->query('post_uuid'));
        } catch (HttpException| InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // try {
        //     $post = $this->postsRepository->get($postUuid);
        // } catch (LikeNotFoundException $exception) {
        //     return new ErrorResponse($exception->getMessage());
        // }

        try {
            $like = $this->likeRepository->getByPostUuid($postUuid);
        } catch (LikeNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            ':uuid' => $like->getUuid(),
            ':post_uuid' => $like->getPost()->getUuid(),
            ':author_uuid' => $like->getUser()->uuid(), 
        ]);
    }

}