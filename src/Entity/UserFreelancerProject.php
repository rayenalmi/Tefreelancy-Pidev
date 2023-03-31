<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * UserFreelancerProject
 *
 * @ORM\Table(name="user_freelancer_project")
 * @ORM\Entity
 */
class UserFreelancerProject
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
     * @ORM\Column(name="id_project", type="integer", nullable=false)
     */
    private $idProject;


}
