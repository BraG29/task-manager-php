<?php

namespace App\Domain\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'creatable_type', type: 'string')]
#[ORM\DiscriminatorMap(['task' => Task::class, 'project' => Project::class])]
abstract class Creatable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id;
    #[ORM\Column(type: 'string')]
    protected string $title;
    #[ORM\Column(type: 'string')]
    protected string $description;
    #[ORM\OneToMany(targetEntity: Link::class, mappedBy: 'creatable', cascade: ['remove'], orphanRemoval: true)]
    protected Collection $links;

    /**
     * @param int|null $id
     * @param string $title
     * @param string $description
     */
    public function __construct(int | null   $id,
                                string $title,
                                string $description)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->links = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void{
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function setLinks(Collection $links): void
    {
        $this->links = $links;
    }

}