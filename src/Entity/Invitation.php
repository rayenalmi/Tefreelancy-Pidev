<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Invitation
 *
 * @ORM\Table(name="invitation")
 * @ORM\Entity
 */
class Invitation
{
    /**
     * @var int
     *
     * @ORM\Column(name="idGroup", type="integer", nullable=false)
     */
    private $idgroup;

    /**
     * @var int
     *
     * @ORM\Column(name="idUser", type="integer", nullable=false)
     */
    private $iduser;

    /**
     * @var string
     *
     * @ORM\Column(name="Status", type="text", length=65535, nullable=false, options={"default"="Pending"})
     */
    private $status = 'Pending';

    /**
     * @var int
     *
     * @ORM\Column(name="idInvitation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idinvitation;


}
