<?php



use Doctrine\ORM\Mapping as ORM;

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
    private $idTest;

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


}
