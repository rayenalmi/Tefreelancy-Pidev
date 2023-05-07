<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Test
 *
 * @ORM\Table(name="test")
 * @ORM\Entity
 */
class Test
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_test", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    #[Groups("tests")]
    private $idTest;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */
    #[Groups("tests")]
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", length=65535, nullable=false)
     */
    #[Groups("tests")]
    private $type;

    public function __toString()
{
    return (string) $this->getIdTest();
}

    public function getIdTest(): ?int
    {
        return $this->idTest;
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


}