<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\UsersRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;

/**
 * Summary of InMemoryUsersRepository
 */
class InMemoryUsersRepository implements UsersRepositoryInterface
{

    private array $users = [];


    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function get(UUID $id): User
    {
        foreach ($this->users as $user) {
            if ($user->id() === $id) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $id");
    }


    // public function getByUsername(string $username): User
    // {
    //     // TODO: Implement getByUsername() method.
    // }
	/**
	 * @param string $username
	 * @return User
	 */
	public function getByUsername(string $username): User {
	}
}