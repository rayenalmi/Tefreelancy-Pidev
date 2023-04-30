<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkspaceTask
 *
 * @ORM\Table(name="workspace_task", uniqueConstraints={@ORM\UniqueConstraint(name="task_id", columns={"task_id"})})
 * @ORM\Entity
 */
class WorkspaceTask
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
     * @ORM\Column(name="task_id", type="integer", nullable=false)
     */
    private $taskId;


}
