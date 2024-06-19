<?php

namespace App\Interface;

use App\Domain\Entities\Enums\RoleType;
use App\Interface\Dtos\UserDTO;
use App\Interface\Dtos\ProjectDTO;
use Couchbase\Role;
use Exception;

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

    public function getUsersByProject(int $projectId);

    /**
     * Función para registrar un nuevo usuario en el sistema.
     * @param UserDTO $userDTO -> datos del usuario a registrar.
     * @return int
     */
    public function registerUser(UserDTO $userDTO): int;

    /**
     * Función para que un usuario inicie sesion en el sistema.
     * @param String $email -> email del usuario.
     * @param String $password -> contraseña del usuario
     * @return UserDTO|null -> retorna una respues afirmativa en caso de encontrar los datos enviados.
     * @throws Exception
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
     * @param RoleType $role
     * @return mixed
     */
    public function linkUserToProject(int $userOwnerId, int $userInvitedId, int $projectId, RoleType $role): void;

    public function verifyEmail(int $userId): bool;

    public function updateRole(int $projectId, RoleType $role, int $userId): void;
    /*
     *
getUsuariosEnProyecto(projectId: id): Set<User>
invitarAProyecto(emisor: User, receptor: Usuario, mensaje: String)
vincularAProyecto(user: Usuario)
asignarRol(proyectoId: long, rol: Rol, user: User)
updateUser(user: User)
     * */
}