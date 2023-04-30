<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rayen
 *
 * @ORM\Table(name="rayen")
 * @ORM\Entity
 */
class Rayen
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
     * @var string
     *
     * @ORM\Column(name="testName", type="string", length=250, nullable=false)
     */
    private $testname;


}
