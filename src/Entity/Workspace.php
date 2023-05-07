<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Workspace
 *
 * @ORM\Table(name="workspace", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="fsk_workspace", columns={"id_offer"})})
 * @ORM\Entity
 */
class Workspace
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"workspaces"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     * @Groups({"workspaces"})
     */
    //#[Assert\NotBlank(message:"Name is required")]
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     * @Groups({"workspaces"})
     */
    //#[Assert\NotBlank(message:"Description is required")]
    private $description;

    // /**
    //  * @var string|null
    //  *
    //  * @ORM\Column(name="task", type="text", length=65535, nullable=true)
    //  * @Groups({"workspaces"})
    //  */
    // private $task;

    // /**
    //  * @var string|null
    //  *
    //  * @ORM\Column(name="publication", type="text", length=65535, nullable=true)
    //  * @Groups({"workspaces"})
    //  */
    // private $publication;

    // /**
    //  * @var string|null
    //  *
    //  * @ORM\Column(name="freelancers", type="text", length=65535, nullable=true)
    //  * @Groups({"workspaces"})
    //  */
    // private $freelancers;



    private $notifications;
    public function getNotifications(): array
    {
        return $this->notifications ?? [];
    }
    public function setNotifications(array $notifications): self
    {
        $this->notifications = $notifications;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    // public function getTask(): ?string
    // {
    //     return $this->task;
    // }

    // public function setTask(?string $task): self
    // {
    //     $this->task = $task;

    //     return $this;
    // }

    // public function getPublication(): ?string
    // {
    //     return $this->publication;
    // }

    // public function setPublication(?string $publication): self
    // {
    //     $this->publication = $publication;

    //     return $this;
    // }

    // public function getFreelancers(): ?string
    // {
    //     return $this->freelancers;
    // }

    // public function setFreelancers(?string $freelancers): self
    // {
    //     $this->freelancers = $freelancers;

    //     return $this;
    // }

    // public function getIdoffer(): ?int
    // {
    //     return $this->idoffer;
    // }

    // public function setIdoffer(int $idoffer): self
    // {
    //     $this->idoffer = $idoffer;

    //     return $this;
    // }


}
