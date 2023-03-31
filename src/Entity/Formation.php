<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Formation
 *
 * @ORM\Table(name="formation")
 * @ORM\Entity
 */
class Formation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_formation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idFormation;

    /**
     * @var int
     *
     * @ORM\Column(name="nbH", type="integer", nullable=false)
     */
    private $nbh;

    /**
     * @var int
     *
     * @ORM\Column(name="nbL", type="integer", nullable=false)
     */
    private $nbl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="path", type="string", length=250, nullable=true)
     */
    private $path;


}
