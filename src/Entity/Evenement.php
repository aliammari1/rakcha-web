<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Evenement
 *
 * @ORM\Table(name="evenement", indexes={@ORM\Index(name="cle_secondaire", columns={"id_categorie"})})
 * @ORM\Entity
 */
class Evenement
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
     * @ORM\Column(name="nom", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="Le nom ne peut pas être vide")
     * @Assert\Length(max=500, maxMessage="Le nom ne peut pas dépasser {{ limit }} caractères")
       * @Assert\Regex(
 *     pattern="/^[a-zA-Z]+$/",
 *     message="Le nom ne peut contenir que des lettres"
 * )
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="date", nullable=false)
     */
    private $datedebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="date", nullable=false)
     */
    private $datefin;

    /**
     * @var string
      * @Assert\NotBlank(message="Le lieu ne peut pas être vide")
     * @Assert\Length(max=500, maxMessage="Le nom ne peut pas dépasser {{ limit }} caractères")
      * @Assert\Regex(
 *     pattern="/^[a-zA-Z]+$/",
 *     message="Le lieu ne peut contenir que des lettres"
 * )
     * @ORM\Column(name="lieu", type="string", length=500, nullable=false)
     */
    private $lieu;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=500, nullable=false)
     */
    private $etat;

    /**
     * @var string
      * @Assert\NotBlank(message="Le description ne peut pas être vide")
     * @Assert\Length(max=500, maxMessage="Le nom ne peut pas dépasser {{ limit }} caractères")
     * @ORM\Column(name="description", type="string", length=500, nullable=false)
      * @Assert\Regex(
 *     pattern="/^[a-zA-Z]+$/",
 *     message="Le description ne peut contenir que des lettres"
 * )
     */
    private $description;

    /**
     * @var string
      * @Assert\NotBlank(message="Le affiche event ne peut pas être vide")
     * @Assert\Length(max=10, maxMessage="Le nom ne peut pas dépasser {{ limit }} caractères")
     * @ORM\Column(name="affiche_event", type="string", length=255, nullable=false)
     */
    private $afficheEvent;

    /**
     * @var \CategorieEvenement
     *
     * @ORM\ManyToOne(targetEntity="CategorieEvenement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categorie", referencedColumnName="ID")
     * })
     */
    private $idCategorie;

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
     * Get the value of nom
     *
     * @return string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     *
     * @param string $nom
     * @return self
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of datedebut
     *
     * @return \DateTime
     */
    public function getDatedebut(): ?\DateTime
    {
        return $this->datedebut;
    }

    /**
     * Set the value of datedebut
     *
     * @param \DateTime $datedebut
     * @return self
     */
    public function setDatedebut(\DateTime $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    /**
     * Get the value of datefin
     *
     * @return \DateTime
     */
    public function getDatefin(): ?\DateTime
    {
        return $this->datefin;
    }

    /**
     * Set the value of datefin
     *
     * @param \DateTime $datefin
     * @return self
     */
    public function setDatefin(\DateTime $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * Get the value of lieu
     *
     * @return string
     */
    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    /**
     * Set the value of lieu
     *
     * @param string $lieu
     * @return self
     */
    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get the value of etat
     *
     * @return string
     */
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    /**
     * Set the value of etat
     *
     * @param string $etat
     * @return self
     */
    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of afficheEvent
     *
     * @return string
     */
    public function getAfficheEvent(): ?string
    {
        return $this->afficheEvent;
    }

    /**
     * Set the value of afficheEvent
     *
     * @param string $afficheEvent
     * @return self
     */
    public function setAfficheEvent(string $afficheEvent): self
    {
        $this->afficheEvent = $afficheEvent;

        return $this;
    }

    /**
     * Get the value of idCategorie
     *
     * @return \CategorieEvenement
     */
    public function getIdCategorie(): ?CategorieEvenement
    {
        return $this->idCategorie;
    }

    /**
     * Set the value of idCategorie
     *
     * @param \CategorieEvenement $idCategorie
     * @return self
     */
    public function setIdCategorie(?CategorieEvenement $idCategorie): self
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }
}
