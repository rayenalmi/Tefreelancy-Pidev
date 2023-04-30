<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Candidacy
 *
 * @ORM\Table(name="candidacy", indexes={@ORM\Index(name="id_offer", columns={"id_offer"}), @ORM\Index(name="id_freelancer", columns={"id_freelancer"})})
 * @ORM\Entity
 */
class Candidacy
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_candidacy", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCandidacy;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=300, nullable=false)
     */
    private $object;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private $message;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="accepted", type="boolean", nullable=true)
     */
    private $accepted;

    /**
     * @var \Offer
     *
     * @ORM\ManyToOne(targetEntity="Offer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_offer", referencedColumnName="id_offer")
     * })
     */
    private $idOffer;

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
