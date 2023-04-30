<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserFreelancerSkill
 *
 * @ORM\Table(name="user_freelancer_skill")
 * @ORM\Entity
 */
class UserFreelancerSkill
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_portfolio", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPortfolio;

    /**
     * @var int
     *
     * @ORM\Column(name="id_skills", type="integer", nullable=false)
     */
    private $idSkills;


}
