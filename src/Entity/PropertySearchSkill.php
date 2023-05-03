<?php
namespace App\Entity; 


class PropertySearchSkill{

    /**
     * @var string|null
     *
     */
    private $level;



    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }



}