<?php

namespace App\Interface\Dtos;
use JsonSerializable;

abstract class CreatableDTO implements JsonSerializable{
    protected int | null $id;
    protected string | null $title;
    protected string | null $description;
    protected array | null $links;

    /**
     * @param int|null $id
     * @param string|null $title
     * @param string|null $description
     * @param array|null $links
     */
    public function __construct(int | null $id,
                                string | null $title,
                                string | null $description,
                                array | null $links)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->links = $links;
    }


    /**
     * @return int|null
     */
    public function getId(): int | null
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
     * @return string|null
     */
    public function getTitle(): string | null
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
     * @return string|null
     */
    public function getDescription(): string | null
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
     * @return array|null
     */
    public function getLinks(): array | null
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