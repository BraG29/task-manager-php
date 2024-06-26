<?php

namespace App\Interface\Dtos;

abstract class CreatableDTO{
    protected int $id;
    protected string $title;
    protected string $description;
    protected array $links;

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @param array $links
     */
    public function __construct(int $id, string $title, string $description, array $links)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->links = $links;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }


}