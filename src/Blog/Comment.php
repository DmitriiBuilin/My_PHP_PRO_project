<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Person\Person;
use GeekBrains\LevelTwo\Blog\Post;

class Comment
{
    private int $id;
    private Person $author;
    private int $postId;
    private string $text;
    // private int $postId = $post->id();
    
    

    public function __construct(
        int $id,
        Person $author,
        int $postId,
        string $text
    )
    {
        $this->id = $id;
        $this->author = $author;        
        $this->postId = $postId; 
        $this->text = $text;
    }

    
    public function __toString()
    {
        return $this->author . ' пишет комментарий к статье №: ' . $this->postId . PHP_EOL . '- ' . $this->text . PHP_EOL;
    }
}