<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grouppostlikes
 *
 * @ORM\Table(name="grouppostlikes")
 * @ORM\Entity
 */
class Grouppostlikes
{
    /**
     * @var int
     *
     * @ORM\Column(name="IdGroupPost", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idgrouppost;

    /**
     * @var int
     *
     * @ORM\Column(name="IdUser", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $iduser;

    /**
     * @var int
     *
     * @ORM\Column(name="idGroup", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idgroup;

    public function getIdgrouppost(): ?int
    {
        return $this->idgrouppost;
    }
    public function setIdgrouppost(int $idgrouppost): self
    {
        $this->idgrouppost = $idgrouppost;

        return $this;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }
    public function setIduser(int $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }
    public function getIdgroup(): ?int
    {
        return $this->idgroup;
    }

    public function setIdgroup(int $Idgroup): self
    {
        $this->idgroup = $Idgroup;

        return $this;
    }
}
