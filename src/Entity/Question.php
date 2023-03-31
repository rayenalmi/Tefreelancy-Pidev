<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question", indexes={@ORM\Index(name="fk_test", columns={"id_test"})})
 * @ORM\Entity
 */
class Question
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_question", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idQuestion;

    /**
     * @var string
     *
     * @ORM\Column(name="quest", type="text", length=65535, nullable=false)
     */
    private $quest;

    /**
     * @var string
     *
     * @ORM\Column(name="choice1", type="text", length=65535, nullable=false)
     */
    private $choice1;

    /**
     * @var string
     *
     * @ORM\Column(name="choice2", type="text", length=65535, nullable=false)
     */
    private $choice2;

    /**
     * @var string
     *
     * @ORM\Column(name="choice3", type="text", length=65535, nullable=false)
     */
    private $choice3;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="text", length=65535, nullable=false)
     */
    private $response;

    /**
     * @var int
     *
     * @ORM\Column(name="id_test", type="integer", nullable=false)
     */
    private $idTest;


}
