<?php

namespace App\Domain\Entities;

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
    protected int $id;
    #[ORM\Column(type: 'string')]
    protected string $title;
    #[ORM\Column(type: 'string')]
    protected string $description;
    #[ORM\OneToMany(targetEntity: Link::class, mappedBy: 'creatable')]
    protected array $links;

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @param array $links
     */
    public function __construct(int    $id,
                                string $title,
                                string $description,
                                array  $links)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->links = $links;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
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

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

}