<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;

class Post
{
  

    public function __construct(
        private UUID $uuid,
        private User $user,
        private string $title,
        private string $text,
    )
    {
    }

    /**
     * @param UUID $uuid
     */ 
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return self
     */ 
    public function setUuid(UUID $uuid): void
    {
            $this->uuid = $uuid;
    }

    /**
     * @param User $user
     */
    public function getUser(): User
    {
        return $this->user;
    }
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
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
        return $this->user->username() . ' пишет: ' . PHP_EOL . '-- ' . $this->title . ' --' . PHP_EOL . $this->text . PHP_EOL;
    }    


}