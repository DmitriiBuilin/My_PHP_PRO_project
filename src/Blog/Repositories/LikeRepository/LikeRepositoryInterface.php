<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\LikeRepository;

use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Like;

interface LikeRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $uuid): array;
}