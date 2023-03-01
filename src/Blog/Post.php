<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Blog\User;

class Post
{
    private int $id;
    private User $user;
    private string $title;
    private string $text;    

    public function __construct(
        int $id,
        User $user,
        string $title,
        string $text
    )
    {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;        
        
        // var_dump($author);
    }

        /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

        /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): Post
    {
        $this->title = $title;
        return $this;
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
    public function setText(string $text): Post
    {
        $this->text = $text;
        return $this;
    }

    public function __toString()
    {
        return $this->author . ' пишет: ' . PHP_EOL . '-- ' . $this->title . ' --' . PHP_EOL . $this->text . PHP_EOL;
    }
    
    public function id(): int
    {
        return $this->id;
    }
}