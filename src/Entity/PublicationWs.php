<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * PublicationWs
 *
 * @ORM\Table(name="publication_ws")
 * @ORM\Entity
 */
class PublicationWs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=30, nullable=false)
     */
   // #[Assert\NotBlank(message:"Title is required")]
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=false)
     */
   // #[Assert\NotBlank(message:"Content is required")]
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=50, nullable=false)
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="date", nullable=false)
     */
    private $creationdate;

    public function __construct()
{
    $this->creationdate = new \DateTime();
}


 /**
     * @var int
     */
    private $likes_count = 0;

    public function getLikesCount(): int
    {
        return $this->likes_count;
    }

    public function incrementLikesCount(): void
    {
        $this->likes_count++;
    }

    
    /**
     * @var boolean
     */


    private $isLikedByUser = false;

    public function getIsLikedByUser(): bool
    {
        return $this->isLikedByUser;
    }

    public function setIsLikedByUser(bool $isLikedByUser): self
    {
        $this->isLikedByUser = $isLikedByUser;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreationdate(): ?\DateTimeInterface
    {
        return $this->creationdate;
    }

    public function setCreationdate(\DateTimeInterface $creationDate = null): self
{
    $this->creationdate = $creationDate ?: new \DateTime();

    return $this;
}

    // public function setCreationdate(\DateTimeInterface $creationdate): self
    // {
    //     $this->creationdate = $creationdate;

    //     return $this;
    // }
    

}
