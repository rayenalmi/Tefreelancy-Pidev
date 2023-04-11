<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommunityRepository;
use Symfony\Component\Validator\Constraints as Assert;

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
    private ?int $idCommunity;
    /**
     * @Assert\NotBlank(message=" name should not be empty")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" enter a name minimum 5 letters"
     *
     *     )
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */

    #[ORM\Column(length: 550)]
    private ?string $name;
    /**
     * @Assert\NotBlank(message="description  doit etre non vide")
     * @Assert\Length(
     *      min = 7,
     *      max = 100,
     *      minMessage = "doit etre >=7 ",
     *     maxMessage = "doit etre <=100" )
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    #[ORM\Column(length: 550)]
    private ?string $description;

    public function getIdCommunity(): ?int
    {
        return $this->idCommunity;
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
