<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * History
 *
 * @ORM\Table(name="history", indexes={@ORM\Index(name="fk_history", columns={"id_freelancer"})})
 * @ORM\Entity
 */
class History
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_history", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idHistory;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", precision=10, scale=0, nullable=false)
     */
    private $rating;

    /**
     * @var float
     *
     * @ORM\Column(name="budget", type="float", precision=10, scale=0, nullable=false)
     */
    private $budget;

    /**
     * @var string
     *
     * @ORM\Column(name="achivements", type="text", length=65535, nullable=false)
     */
    private $achivements;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_freelancer", referencedColumnName="id_user")
     * })
     */
    private $idFreelancer;


}
