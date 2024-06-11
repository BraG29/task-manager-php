<?php

namespace App\Application\Controllers;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Interface\Dtos\UserDTO;
use App\Interface\UserController;

class UserControllerImpl implements UserController
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function getUsers(): array
    {
        $users = $this->userRepository->findAll();
        $usersOutput = [];
        foreach ($users as $user) {
            $usersOutput[] = new UserDTO($user);

        }

        return $usersOutput;
    }

    /**
     * @inheritDoc
     */
    public function getUser(int $id): ?UserDTO
    {
        return new UserDTO(
            $this->userRepository->findById($id)
        );
    }
}