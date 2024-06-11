<?php

namespace App\Interface;

use App\Interface\Dtos\UserDTO;

interface UserController
{
    /**
     * @return array
     */
    public function getUsers() : array;

    /**
     * @param int $id
     * @return UserDTO|null
     */
    public function getUser(int $id) : ?UserDTO;
}