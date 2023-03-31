<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     */
    private $firstName;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="integer", nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=250, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=50, nullable=false)
     */
    private $photo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="role", type="string", length=0, nullable=true)
     */
    private $role;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Offer", mappedBy="idUser")
     */
    private $idOffer = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idOffer = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
