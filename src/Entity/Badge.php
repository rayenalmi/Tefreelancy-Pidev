<?php



use Doctrine\ORM\Mapping as ORM;

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
    private $idBadge;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", length=65535, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @var \Test
     *
     * @ORM\ManyToOne(targetEntity="Test")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_test", referencedColumnName="id_test")
     * })
     */
    private $idTest;


}
