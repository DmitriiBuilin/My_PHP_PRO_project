<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostRepository;

use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
}