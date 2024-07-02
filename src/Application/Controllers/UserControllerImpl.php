<?php

namespace App\Application\Controllers;


use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\Link;
use App\Domain\Entities\Project;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\ProjectRepository;
use App\Domain\Repositories\LinkRepository;
use App\Interface\Dtos\UserDTO;
use App\Interface\UserController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;


class UserControllerImpl implements UserController
{
    private UserRepository $userRepository;
    private ProjectRepository $projectRepository;
    private LinkRepository $linkRepository;

    public function __construct(UserRepository $userRepository, ProjectRepository $projectRepository,
    LinkRepository $linkRepository){

        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->linkRepository = $linkRepository;
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


    public function getUsersByProject(int $projectId) : ?array
    {
        try{
            $project = $this->projectRepository->findById($projectId);

            if($project == null){
                throw new Exception("No se pudo encontrar el proyecto.");
            }

            /** @var ArrayCollection|Link[] $links */
            $links = $project->getLinks();

            $users = [];

            foreach ($links as $link){
                $user = $link->getUser();

                $users[] = new UserDTO($user);

            }
            return $users;

        }catch(Exception $e){
            throw $e;
        }

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

    public function inviteUserToProject(int $senderId, string $receiverEmail, int $projectId, RoleType $role): void
    {

        $receiver = $this->userRepository->findByEmail($receiverEmail);
        $sender = $this->userRepository->findById($senderId);

        if($receiverEmail == null || $senderId == null){
            throw new Exception("Ocurrio un error al buscar usuarios");
        }

        if($receiver->getId() == $senderId){
            throw new Exception("No puedes agregarte al proyecto.");
        }

        $project = $this->projectRepository->findById($projectId);

        if ($project === null) {
            throw new Exception("Proyecto no encontrado.");
        }


        $subject = "Te han invitado a un proyecto!";

        $queryParams = [
            'userOwnerId' => $senderId,
            'invitedEmail' => $receiverEmail,
            'projectId' => $projectId,
            'role' => $role,
            'action' => 'accepted'
        ];

        $invitationAccepted = "http://localhost:8080/linkUser?" . http_build_query($queryParams);
        $invitationRejected = "http://localhost:8080/linkUser?action=rejected";

        $senderName = $sender->getName();
        $receiverName = $receiver->getName();
        $projectName = $project->getTitle();


        $message = "
                <html>
                <body>
                    <p>Hola! $receiverName, el usuario: 
                    $senderName te ha invitado a unirte a su proyecto $projectName</p>
                    <p>Haz clic en uno de los siguientes botones, para aceptar o rechazar la invitacion:</p>
                    
                    <a href='$invitationAccepted'>
                        <button style='padding: 15px 20px; color: white; background-color: blue; border: none; border-radius: 5px;'>Aceptar invitacion</button>
                    </a>
                    
                     <a href='$invitationRejected'>
                        <button style='padding: 15px 20px; color: white; background-color: blue; border: none; border-radius: 5px;'>Rechazar invitacion</button>
                    </a>
                   
                </body>
                </html>
            ";

        $sendMail = require __DIR__ . '/../../../app/sendEmail.php';
        $sendMail($receiverEmail, $subject, $message);
    }

    public function linkUserToProject(int $userOwnerId, string $userInvitedEmail, int $projectId, RoleType $role): void
    {
        try{
            $userInvited = $this->userRepository->findByEmail($userInvitedEmail);
            $projectOwner = $this->userRepository->findById($userOwnerId);

            if($userInvited == null || $projectOwner == null){
                throw new Exception("Ocurrio un error al buscar usuarios");
            }

            if ($this->isUserLinkedToProject($userInvited, $projectId)) {
                throw new Exception("El usuario ya se encuentra vinculado al proyecto.");
            }


            /** @var ArrayCollection|Link[] $links */
            $links = $projectOwner->getLinks();

            foreach ($links as $link){
                //si usuario tiene un vinculo con el projecto y es ADMIN del mismo.

                $project = $this->projectRepository->findById($projectId);

                //con esto me aseguro que sea un proyecto.
                if($project == null){
                    throw new Exception("No se encontro un proyecto con ese id.");
                }

                if($link->getCreatable()->getId() == $projectId &&  $link->getRole() == RoleType::ADMIN){

                    if($role == RoleType::ADMIN){
                        throw new Exception("No puedas dar rol de administrador");
                    }

                    $newLink = new Link(null, new \DateTimeImmutable(), $role, $project, $userInvited);
                    $this->linkRepository->save($newLink);
                    $this->userRepository->save($userInvited);

                }
            }

        }catch (Exception $e){
            throw $e;
        }

    }

    //Funcion auxiliar de linkUserToProject
    private function isUserLinkedToProject($user, int $projectId): bool
    {
        foreach ($user->getLinks() as $link) {
            if ($link->getCreatable()->getId() === $projectId) {
                return true;
            }
        }
        return false;
    }

    public function registerUser(UserDTO $userDTO): int
    {
        if($this->userRepository->findByEmail($userDTO->getEmail()) != null){
            throw new Exception("El correo se encuentra en uso");
        }

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

            $verificationLink = "http://localhost:8080/verifyEmail?userId=".$userId;
            $htmlPage = file_get_contents(__DIR__ . '/../../../public/pages/verificationEmail.html');
            $html = str_replace(['{name}', '{verificationLink}'], [$name, $verificationLink], $htmlPage);
            $sendMail = require __DIR__ . '/../../../app/sendEmail.php';
            $sendMail($receiver, $subject, $html);

            return $user->getId();

        } catch (Exception $e) {
            echo "Error persistiendo: " . $e->getMessage();
            throw $e;
        }
    }

    public function verifyEmail(int $userId): bool
    {
        try{

            $user = $this->userRepository->findById($userId);
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

    public function updateRole(int $projectId, RoleType $role, int $userId): void
    {
        try{
            $project = $this->projectRepository->findById($projectId);

            if($role = RoleType::ADMIN){
                throw new Exception("No puedes dar rol de administardor");
            }

            /** @var ArrayCollection|Link[] $links */    //esto indica que estoy esperando una lista de ORM.
            $links = $project->getLinks();
            foreach ($links as $link) {
                if ($link->getUser()->getId() == $userId) {
                    $link->setRole($role);
                    $this->linkRepository->save($link);
                }
            }
        }catch (Exception $e){
            throw $e;
        }
    }

    public function updateUser(UserDTO $userDTO) : int{
        try{

            $user = $this->userRepository->findById($userDTO->getId());

            if($user == null){
                throw new Exception ("No se pudo encontrar el usuario");
            }

            $user->setName($userDTO->getName());
            $user->setLastName($userDTO->getLastName());
            $user->setPassword($userDTO->getPassword());

            $this->userRepository->save($user);
            return $user->getId();

        }catch(Exception $e){
            throw $e;
        }
    }


}