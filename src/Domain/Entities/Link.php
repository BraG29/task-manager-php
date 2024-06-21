<?php

namespace App\Domain\Entities;

use App\Domain\Entities\Enums\RoleType;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * <p>
 *    <b>Entidad que representa el vinculo entre usuario y creable</b>
 * </p>
 * <p>
 *  Tipo de vinculos:
 * </p>
 * <ul>
 *     <li>
 *         Usuario crea Proyecto
 *     </li>
 *     <li>
 *         Usuario es parte de Proyecto
 *     </li>
 *     <li>
 *         Usuario crea Tarea
 *     </li>
 *     <li>
 *         Usuario es responsable de Tarea
 *     </li>
 * </ul>
 * @see RoleType
 */
#[ORM\Entity]
#[ORM\Table(name: 'Link')]
class Link
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id;
    #[ORM\Column(name: 'link_date', type: 'datetime_immutable')]
    private DateTimeImmutable $linkDate; //TODO: cambiar nombre de la variable
    #[ORM\Column(enumType: RoleType::class)]
    private RoleType $role;
    #[ORM\ManyToOne(targetEntity: Creatable::class, inversedBy: 'links')]
    private Creatable $creatable;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'links')]
    private User $user;

    /**
     * @param int|null $id
     * @param DateTimeImmutable $creationDate
     * @param RoleType $role
     * @param Creatable $creatable
     * @param User $user
     */
    public function __construct(?int               $id,
                                DateTimeImmutable $creationDate,
                                RoleType          $role,
                                Creatable         $creatable,
                                User              $user)
    {
        $this->id = $id;
        $this->linkDate = $creationDate;
        $this->role = $role;
        $this->creatable = $creatable;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getLinkDate(): DateTimeImmutable
    {
        return $this->linkDate;
    }

    public function setLinkDate(DateTimeImmutable $linkDate): void
    {
        $this->linkDate = $linkDate;
    }

    public function getRole(): RoleType
    {
        return $this->role;
    }

    public function setRole(RoleType $role): void
    {
        $this->role = $role;
    }

    public function getCreatable(): Creatable
    {
        return $this->creatable;
    }

    public function setCreatable(Creatable $creatable): void
    {
        $this->creatable = $creatable;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}