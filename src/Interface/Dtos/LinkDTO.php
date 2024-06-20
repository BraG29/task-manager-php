<?php

namespace App\Interface\Dtos;

use App\Domain\Entities\Enums\RoleType;
use App\Domain\Entities\User;
use DateTimeImmutable;
use JsonSerializable;

class LinkDTO implements JsonSerializable{

    private ?int $id;
    private DateTimeImmutable $creationDate;
    private RoleType $role;
    private CreatableDTO $creatableDTO;
    private User $user;

    public function __construct(?int $id, DateTimeImmutable $creationDate, RoleType $role, CreatableDTO $creatableDTO, User $user)
    {
        $this->id = $id;
        $this->creationDate = $creationDate;
        $this->role = $role;
        $this->creatableDTO = $creatableDTO;
        $this->user = $user;
    }

    public static function fromArray($data): LinkDTO
    {
        return new LinkDTO(
            $data['id'],
            $data['creationDate'],
            $data['role'],
            $data['creatable'],
            $data['user']
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
     * @return User
     */
    public function getUser(): User
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