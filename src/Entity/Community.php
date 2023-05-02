<?php

namespace App\Entity;

use App\Repository\CommentairegrppostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentairegrppostRepository::class)
 */
class Commentairegrppost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $context;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Idgrppost;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getIdgrppost(): ?int
    {
        return $this->Idgrppost;
    }

    public function setIdgrppost(?int $Idgrppost): self
    {
        $this->Idgrppost = $Idgrppost;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }
}