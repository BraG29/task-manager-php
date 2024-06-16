<?php

namespace App\Application\Controllers;


use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Interface\Dtos\UserDTO;
use App\Interface\UserController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

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

    public function getUsersByProject(int $projectId)
    {
        // TODO: Implement getUsersByProject() method.
    }

    public function signIn(string $email, string $password)
    {
        //si no esta verificado no inicia sesion.
        //busco user en la bd y compruebo estado de verifiacion.
        // TODO: Implement signIn() method.
    }

    public function inviteUserToProject(UserDTO $sender, UserDTO $receiver, RoleType $role)
    {
        // TODO: Implement inviteUserToProject() method.
    }

    public function linkUserToProject(int $userId, int $projectId)
    {
        // TODO: Implement linkUserToProject() method.
    }

    public function registerUser(UserDTO $userDTO): int
    {
        //nuevos atributos
        //Token verificacion.
        //estado verificado.
        $token = bin2hex(random_bytes(8));
        $verified = false;

        $user = new User(
            $userDTO->getId(),
            $userDTO->getName(),
            $userDTO->getLastName(),
            $userDTO->getEmail(),
            $userDTO->getPassword(),
            new ArrayCollection()
        );

        try {
            $this->userRepository->save($user);

            $receiver = $userDTO->getEmail();
            $subject = "Verificación de correo";
            //posible link http://localhost:8080/verifiyEmail?token=
            $verificationLink = "https://comopijaverificar.com/verificar.php?token=" . $token;
            $message = "Haz clic en el siguiente enlace para verificar tu correo electrónico: " . $verificationLink;

            //mensaje llama endpoint de  verificar.
            //TODO oper verificar mail.
            return $user->getId();

        } catch (Exception $e) {
            echo "Error persistiendo: " . $e->getMessage();
            return 0;
        }
    }
}