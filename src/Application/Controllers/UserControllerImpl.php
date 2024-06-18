<?php

namespace App\Application\Controllers;


use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\Link;
use App\Domain\Entities\Token;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Interface\Dtos\UserDTO;
use App\Interface\Dtos\ProjectDTO;
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

    public function signIn(string $email, string $password): ?UserDTO
    {
        $user = $this->userRepository->findByEmail($email);

//            if($user->getToken() != null){
//                throw new Exception("Por favor validate antes de iniciar sesión");
//            }

        if($user
            && substr_compare($user->getEmail(), $email, 0) == 0
            && substr_compare($user->getPassword(), $password, 0) == 0){

            return new UserDTO($user);
        }

        return null;
    }

    public function inviteUserToProject(UserDTO $sender, UserDTO $receiver, ProjectDTO $project, RoleType $role)
    {

        $r = $receiver->getEmail();
        $subject = "Te han invitado a un proyecto!";
        $aceptationLink = "http://localhost:8080/invitationAccepted";
        $message = $sender->getName() . " te ha invitado a unirte a su proyecto: " . $project->getName(). "
        Presiona el link para aceptar: " . $aceptationLink;

        // TODO: ver como enviar al endpoint de linkear usuario desde el mensaje, con los parametros.
        sendMail($r, $subject, $message);

    }

    public function linkUserToProject(int $userOwnerId, int $userId, int $projectId, RoleType $role)
    {
        try{
            $userInvited = $this->userRepository->findById($userId);

            $projectOwner = $this->userRepository->findById($userOwnerId);

            foreach ($projectOwner->getLinks() as $link){
                //si usuario tiene un vinculo con el projecto y es ADMIN del mismo.
                if($link->getCreatable()->getId() == $projectId ||  $link->getRole() == RoleType::ADMIN){
                    $project = $link->getCreatable(); //obtengo proyecto.

                    $newLink = new Link(null, new \DateTimeImmutable(), $role, $project, $userInvited);

                    $userInvited->getLinks()->add($newLink);

                    $this->userRepository->save($userInvited);
                }
            }

        }catch (Exception $e){
            throw $e;
        }

    }

    public function registerUser(UserDTO $userDTO): int
    {

        $tokenGenerator = bin2hex(random_bytes(8));
        $token = new Token(null, $tokenGenerator);

        //TODO hashear contraseña
        $user = new User(
            $userDTO->getId(),
            $userDTO->getName(),
            $userDTO->getLastName(),
            $userDTO->getEmail(),
            $userDTO->getPassword(),
            new ArrayCollection(),
        );
        $user->setToken($token);

        try {
            $this->userRepository->save($user);

            $receiver = $userDTO->getEmail();
            $subject = "Verificación de correo";
            $verificationLink = "http://localhost:8080/verifiyEmail?token=" . $token->getToken();
            $message = "Haz clic en el siguiente enlace para verificar tu correo electrónico: " . $verificationLink;
            $sendMail = require __DIR__ . '/../../../public/sendEmail.php';

            $sendMail($receiver, $subject, $message);
            return $user->getId();

        } catch (Exception $e) {
            echo "Error persistiendo: " . $e->getMessage();
            return 0;
        }
    }

    //TODO oper verificar email.

}