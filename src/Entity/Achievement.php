<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Achievement
 *
 * @ORM\Table(name="achievement", indexes={@ORM\Index(name="id_offer", columns={"id_offer"})})
 * @ORM\Entity
 */
class Achievement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_achivement", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAchivement;

    /**
     * @var int
     *
     * @ORM\Column(name="id_freelancer", type="integer", nullable=false)
     */
    private $idFreelancer;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float", precision=10, scale=0, nullable=false)
     */
    private $rate;

    /**
     * @var \Offer
     *
     * @ORM\ManyToOne(targetEntity="Offer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_offer", referencedColumnName="id_offer")
     * })
     */
    private $idOffer;


}
