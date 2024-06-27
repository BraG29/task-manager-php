<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Enums\RoleType;
use DateTimeImmutable;
use JsonSerializable;

class LinkDTO implements JsonSerializable{

    private ?int $id;
    private DateTimeImmutable $creationDate;
    private RoleType $role;
    private CreatableDTO | null $creatableDTO;
    private ?UserDTO $user;

    public function __construct(?int $id,
                                DateTimeImmutable $creationDate,
                                RoleType $role,
                                CreatableDTO | null $creatableDTO,
                                ?UserDTO $user){
        $this->id = $id;
        $this->creationDate = $creationDate;
        $this->role = $role;
        $this->creatableDTO = $creatableDTO;
        $this->user = $user;
    }

    public static function fromArray($data): LinkDTO{

        $userDTO = UserDTO::fromArray($data['user']);

        return new LinkDTO(
            $data['id'],
            $data['creationDate'],
            $data['role'],
            $data['creatable'],
            $userDTO
        );
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreationDate(): DateTimeImmutable
    {
        return $this->creationDate;
    }

    /**
     * @return RoleType
     */
    public function getRole(): RoleType
    {
        return $this->role;
    }

    /**
     * @return CreatableDTO
     */
    public function getCreatableDTO(): CreatableDTO
    {
        return $this->creatableDTO;
    }

    /**
     * @return UserDTO
     */
    public function getUser(): UserDTO
    {
        return $this->user;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'creationDate' => $this->creationDate,
            'role' => $this->role,
            'creatable' => $this->creatableDTO,
            'user' => $this->user
        ];
    }


}