<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Badge
 *
 * @ORM\Table(name="badge", indexes={@ORM\Index(name="fk_badge", columns={"id_test"})})
 * @ORM\Entity
 */
class Badge
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_badge", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    #[Groups("badges")]
    private $idBadge;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */
    #[Groups("badges")]
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", length=65535, nullable=false)
     */
    #[Groups("badges")]
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    #[Groups("badges")]
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="id_test", type="integer", nullable=false)
     */
    #[Groups("badges")]
    private $idTest;
    public function __toString()
    {
        return (string) $this->getIdTest();
    }

    public function getIdBadge(): ?int
    {
        return $this->idBadge;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIdTest(): ?int
    {
        return $this->idTest;
    }

    public function setIdTest(?int $idTest): self
    {
        $this->idTest = $idTest;

        return $this;
    }


}