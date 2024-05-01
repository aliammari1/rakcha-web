<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParticipationEvenement
 *
 * @ORM\Table(name="participation_evenement", indexes={@ORM\Index(name="cle_secondaire1", columns={"id_evenement"}), @ORM\Index(name="cle_secondaire2", columns={"id_user"})})
 * @ORM\Entity
 */
class ParticipationEvenement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_participation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idParticipation;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \Evenement
     *
     * @ORM\ManyToOne(targetEntity="Evenement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_evenement", referencedColumnName="ID")
     * })
     */
    private $idEvenement;

    /**
     * Get the value of idParticipation
     *
     * @return int
     */
    public function getIdParticipation(): ?int
    {
        return $this->IdParticipation;
    }


    /**
     * Get the value of idEvenement
     *
     * @return \Evenement
     */
    public function getIdEvenement(): ?Evenement
    {
        return $this->idEvenement;
    }


    /**
     * Set the value of idEvenement
     *
     * @param \Evenement $idEvenement
     * @return self
     */
    public function setIdEvenement(?Evenement $idEvenement): self
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }

    /**
     * Get the value of idUser
     *
     * @return int
     */
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    /**
     * Set the value of idUser
     *
     * @param int $idUser
     * @return self
     */
    public function setidUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get the value of quantity
     *
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @param int $description
     * @return self
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }


}
