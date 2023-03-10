<?php

namespace GeekBrains\LevelTwo;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostRepository\SqlitePostRepository;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostRepository($connectionMock);

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: 123e4567-e89b-12d3-a456-426614174025');

        $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174025'));
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
        ->expects($this->once()) 
        ->method('execute') 
        ->with([ 
            ':uuid' => '123e4567-e89b-12d3-a456-426614174025',
            ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':title' => 'Title',
            ':text' => 'Text',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostRepository($connectionStub);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('first_name', 'last_name'),
            'ivan123',
        );

        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174025'),
                $user,
                'Title',
                'Text'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([ 
            ':uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
            ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':title' => 'Title',
            ':text' => 'Text',
            ':username' => 'ivan123',
            ':first_name' => 'Ivan',
            ':last_name' => 'Nikitin',
        ]);

        

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostRepository($connectionStub);
        
        $post = $postRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));

        $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97694', (string)$post->getUuid());
    }
}