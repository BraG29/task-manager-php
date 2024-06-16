<?php

namespace App\Interface;

use App\Domain\Entities\Enums\RoleType;
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
     * @return mixed -> retorna una respues afirmativa en caso de encontrar los datos enviados.
     */
    public function signIn(String $email, String $password);


    /**
     * Función para invitar un usuario a un proyecto.
     * @param UserDTO $sender -> datos del usuario que envia la invitacion.
     * @param UserDTO $receiver -> datos del usuario que recibe la invitacion.
     * @param RoleType $role -> rol que se le quiere dar al usuario.
     * @return mixed
     */
    public function inviteUserToProject(UserDTO $sender, UserDTO $receiver, RoleType $role);

    /**
     * Función para vincular un usuario a un proyecto, cuando este acepta una  invitacion.
     * @param int $userId -> id del usuario a vincular.
     * @param int $projectId -> id del proyecto a vincular.
     * @return mixed
     */
    public function linkUserToProject(int $userId, int $projectId);


    /*
     * registrar(user: User)
iniciarSesion(user: User): boolean
getDatosUsuario(userId: long): User
getUsuariosEnProyecto(projectId: id): Set<User>
getDatosUsuario(): Set<User> -> no se usa a priori
invitarAProyecto(emisor: User, receptor: Usuario, mensaje: String)
vincularAProyecto(user: Usuario)
asignarRol(proyectoId: long, rol: Rol, user: User)
updateUser(user: User)
     * */
}