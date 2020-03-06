<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameCategory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pictureCategory;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCategory(): ?string
    {
        return $this->nameCategory;
    }

    public function setNameCategory(string $nameCategory): self
    {
        $this->nameCategory = $nameCategory;

        return $this;
    }

    public function getPictureCategory(): ?string
    {
        return $this->pictureCategory;
    }

    public function setPictureCategory(string $pictureCategory): self
    {
        $this->pictureCategory = $pictureCategory;

        return $this;
    }
}
