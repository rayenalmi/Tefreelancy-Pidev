<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommunityRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommunityRepository::class)]
/**
 * Community
 *
 * @ORM\Table(name="community")
 * @ORM\Entity
 */
class Community
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_community", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("pagination")]

    private ?int $idCommunity;
    /**
     * @Assert\NotBlank(message=" Name field can't be empty")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Enter a name with minimum 5 letters"
     *
     *     )
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */

    #[ORM\Column(length: 550)]
    #[Groups("pagination")]

    private ?string $name;
    /**
     * @Assert\NotBlank(message="Description field can't be empty")
     * @Assert\Length(
     *      min = 7,
     *      max = 100999,
     *      minMessage = "Should be >=7 ",
     *     maxMessage = "Should be <=100999" )
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    #[ORM\Column(length: 550)]
    #[Groups("pagination")]

    private ?string $description;

    public function getIdCommunity(): ?int
    {
        return $this->idCommunity;
    }
    public function setIdCommunity(int $id): self
    {
        $this->idCommunity = $id;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
