<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;

class Comment
{
    private UUID $uuid;
    private User $user;
    private Post $post;
    private string $text;

    public function __construct(
        UUID $uuid,
        User $user,
        Post $post,
        string $text
    )
    {
        $this->uuid = $uuid;
        $this->user = $user;        
        $this->post = $post; 
        $this->text = $text;
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

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
    
    public function __toString()
    {
        return $this->user->username() . ' пишет комментарий к статье №: ' . $this->postId . PHP_EOL . '- ' . $this->text . PHP_EOL;
    }
}