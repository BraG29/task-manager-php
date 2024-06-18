<?php

namespace App\Application\Controllers;


use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\Link;
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

        if($user
            && substr_compare($user->getEmail(), $email, 0) == 0
            && substr_compare($user->getPassword(), $password, 0) == 0){

            return new UserDTO($user);
        }

        return null;
    }

    public function inviteUserToProject(UserDTO $sender, UserDTO $receiver, ProjectDTO $project, RoleType $role): void
    {

        $r = $receiver->getEmail();
        $subject = "Te han invitado a un proyecto!";
        $aceptationLink = "http://localhost:8080/invitationAccepted";
        $message = $sender->getName() . " te ha invitado a unirte a su proyecto: " . $project->getName(). "
        Presiona el link para aceptar: " . $aceptationLink;

        // TODO: ver como enviar al endpoint de linkear usuario desde el mensaje, con los parametros.
        //sendMail($r, $subject, $message);

    }

    public function linkUserToProject(int $userOwnerId, int $userInvitedId, int $projectId, RoleType $role): void
    {
        try{
            $userInvited = $this->userRepository->findById($userInvitedId);
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

        //TODO hashear contraseña
        $user = new User(
            $userDTO->getId(),
            $userDTO->getName(),
            $userDTO->getLastName(),
            $userDTO->getEmail(),
            $userDTO->getPassword(),
            new ArrayCollection(),
            false
        );

        try {
            $this->userRepository->save($user);

            $receiver = $userDTO->getEmail();
            $name = $userDTO->getName();
            $subject = "Verificacion de correo";
            $userId = $user->getId();

            $verificationLink = "http://192.168.1.15:8080/verifyEmail?userId=".$userId;

            $message = "
                <html>
                <body>
                    <p>Hola! $name bienvenido a nuestra plataforma.</p>
                    <br>
                    <p>Para poder iniciar sesion primero debes verificar tu correo. <br>
                    Haz clic en el siguiente boton para verificar tu correo electronico:</p>
                    <br>
                    <a href='$verificationLink'>
                        <button style='padding: 10px 20px; color: white; background-color: blue; border: none; border-radius: 5px;'>Verificar correo</button>
                    </a>
                </body>
                </html>
            ";

            $sendMail = require __DIR__ . '/../../../app/sendEmail.php';
            $sendMail($receiver, $subject, $message);
            return $user->getId();

        } catch (Exception $e) {
            echo "Error persistiendo: " . $e->getMessage();
            return 0;
        }
    }

    public function verifyEmail(int $userId): bool
    {
        try{

            $user = $this->userRepository->findById($userId);
            var_dump($user->isVerified());
            if(!$user->isVerified()){
                $user->setVerified(true);
                $this->userRepository->save($user);
                return true;
            }else{
                echo "este correo ya esta verificado.";
            }
        } catch (Exception $e){
            throw $e;
        }

        return false;
    }
}