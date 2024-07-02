<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Enums\RoleType;
use DateTimeImmutable;
use JsonSerializable;

class LinkDTO implements JsonSerializable{

    private int | null $id;
    private DateTimeImmutable | null $creationDate;
    private RoleType | null $role;
    private CreatableDTO | null $creatableDTO;
    private UserDTO | null $user;

    public function __construct(int | null  $id,
                                DateTimeImmutable | null  $creationDate,
                                RoleType | null $role,
                                CreatableDTO | null $creatableDTO,
                                UserDTO | null $user){
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
            RoleType::from($data['role']),
            $data['creatable'],
            $userDTO
        );
    }

    /**
     * @return int|null
     */
    public function getId(): int | null
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreationDate(): DateTimeImmutable | null
    {
        return $this->creationDate;
    }

    /**
     * @return RoleType
     */
    public function getRole(): RoleType | null
    {
        return $this->role;
    }

    /**
     * @return CreatableDTO
     */
    public function getCreatableDTO(): CreatableDTO | null
    {
        return $this->creatableDTO;
    }

    /**
     * @return UserDTO
     */
    public function getUser(): UserDTO | null
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