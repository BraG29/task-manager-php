<?php

namespace App\Interface;

use App\Domain\Entities\Enums\RoleType;
use App\Interface\Dtos\UserDTO;


interface UserController
{
    /**
     * Obtiene la lista de todos los usuarios del sistema.
     * @return array -> devuelve la lista de usuarios.
     */
    public function getUsers() : array;

    /**
     * Obtiene un usuario en particuarl.
     * @param int $id -> identificador del usuario.
     * @return UserDTO|null -> devuelve el usuario.
     */
    public function getUser(int $id) : ?UserDTO;

    /**
     * Obtiene la lista de usuarios dentro de un proyecto.
     * @param int $projectId -> id del proyecto a buscar.
     * @return array|null -> devuelve la lista de usuario dentro de un proyecto.
     */
    public function getUsersByProject(int $projectId) : ?array;

    /**
     * Función para registrar un nuevo usuario en el sistema.
     * @param UserDTO $userDTO -> datos del usuario a registrar.
     * @return int -> devuelve el id del usuario creado.
     */
    public function registerUser(UserDTO $userDTO): int;

    /**
     * Función para que un usuario inicie sesion en el sistema.
     * @param String $email -> email del usuario.
     * @param String $password -> contraseña del usuario
     * @return UserDTO|null -> retorna una respues afirmativa en caso de encontrar los datos enviados.
     */
    public function signIn(String $email, String $password): ?UserDTO;


    /**
     * Función para enviar un mail a un suuario invitandolo a un proyecto..
     * @param int $senderId -> id del usuario que envia la invitacion.
     * @param int $receiverId -> id del usuario que recive la invitacion.
     * @param int $projectId -> id del projecto a invitar.
     * @param RoleType $role -> rol que se le quiere dar al usuario.
     *
     */
    public function inviteUserToProject(int $senderId, int $receiverId, int $projectId, RoleType $role): void;

    /**
     * Función para vincular un usuario a un proyecto, cuando este acepta una invitacion.
     * @param int $userOwnerId -> id del usuario dueño del proyecto.
     * @param int $userInvitedId -> id del usuario a invitar.
     * @param int $projectId -> id del proyecto a vincular.
     * @param RoleType $role -> rol que se le asignará al usuario.
     */
    public function linkUserToProject(int $userOwnerId, int $userInvitedId, int $projectId, RoleType $role): void;

    /**
     * Funcíon para verificar el correo de un  usuario.
     * @param int $userId -> identificador del usuario a validar correo.
     * @return bool -> devuelve true si puedo validar, caso contrario devuelve false.
     */
    public function verifyEmail(int $userId): bool;

    /**
     * Función para cambiar el rol de un usuario en un proyecto.
     * @param int $projectId -> identifcador del proyecto.
     * @param RoleType $role -> rol nuevo.
     * @param int $userId -> identificador del usuario a otorgar nuevo rol.
     */
    public function updateRole(int $projectId, RoleType $role, int $userId): void;


    /**
     * Función para actualizar datos de un usuario.
     * @param UserDTO $user -> dto del usuario a modificar.
     * @return int -> id del usuario modificado.
     */
    public function updateUser(UserDTO $userDTO) : int;

}