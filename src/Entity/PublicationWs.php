<?php



use Doctrine\ORM\Mapping as ORM;

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
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=false)
     */
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


}
