<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Person\Person;

class Post
{
    private int $id;
    private Person $author;
    private string $title;
    private string $text;


    

    public function __construct(
        int $id,
        Person $author,
        string $title,
        string $text
    )
    {
        $this->id = $id;
        $this->author = $author;
        $this->title = $title;
        $this->text = $text;        
        
        // var_dump($author);
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