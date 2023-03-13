<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;

class Like
{
    private UUID $uuid;    
    private Post $post;
    private User $user;


    public function __construct(
        UUID $uuid,
        Post $post,
        User $user
    )
    {
        $this->uuid = $uuid;               
        $this->post = $post; 
        $this->user = $user; 
    }

        /**
     * @return int
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param int $id
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    } 
}