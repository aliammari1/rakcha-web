<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Sponsor
 *
 * @ORM\Table(name="sponsor")
 * @ORM\Entity
 */
class Sponsor
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

  
    /**
     * @var string
     *
     * @ORM\Column(name="nomSociete", type="string", length=500, nullable=false, options={"default": ""})
     * @Assert\NotBlank(message="The company name cannot be empty")
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "The company name cannot be longer than {{ limit }} characters"
     * )
     */
    private $nomsociete;

    /**
     * @var string
     *
     * @ORM\Column(name="Logo", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="The logo path cannot be empty")
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The logo path cannot be longer than {{ limit }} characters"
     * )
     */
    private $logo;
/**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Get the value of nomsociete
     *
     * @return string
     */
    public function getNomsociete(): ?string
    {
        return $this->nomsociete;
    }

    /**
     * Set the value of nomsociete
     *
     * @param string $nomsociete
     * @return self
     */
    public function setNomsociete(string $nomsociete): self
    {
        $this->nomsociete = $nomsociete;

        return $this;
    }

    /**
     * Get the value of logo
     *
     * @return string
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }
 public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    // Other getter and setter methods for $id and $logo
}
