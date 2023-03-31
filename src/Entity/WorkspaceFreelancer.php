<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * WorkspaceFreelancer
 *
 * @ORM\Table(name="workspace_freelancer", uniqueConstraints={@ORM\UniqueConstraint(name="workspace-freelancer", columns={"workspace_id", "freelancer_id"})})
 * @ORM\Entity
 */
class WorkspaceFreelancer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="workspace_id", type="integer", nullable=false)
     */
    private $workspaceId;

    /**
     * @var int
     *
     * @ORM\Column(name="freelancer_id", type="integer", nullable=false)
     */
    private $freelancerId;


}
