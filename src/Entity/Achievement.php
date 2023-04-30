<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achievement
 *
 * @ORM\Table(name="achievement")
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
     * @var int
     *
     * @ORM\Column(name="id_offer", type="integer", nullable=false)
     */
    private $idOffer;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float", precision=10, scale=0, nullable=false)
     */
    private $rate;


}
