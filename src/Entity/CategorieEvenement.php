<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CategorieEvenement
 *
 * @ORM\Table(name="categorie_evenement")
 * @ORM\Entity
 */
class CategorieEvenement
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
     * @ORM\Column(name="Nom_categorie", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="Le nom de la catégorie ne peut pas être vide")
     * @Assert\Length(max=500, maxMessage="Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     message="Le nom de la catégorie ne peut contenir que des lettres"
     * )
     */
    private $nomCategorie;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="La description ne peut pas être vide")
     * @Assert\Length(max=500, maxMessage="La description ne peut pas dépasser {{ limit }} caractères")
     */
    private $description;

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
     * Get the value of nomCategorie
     *
     * @return string
     */
    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    /**
     * Set the value of nomCategorie
     *
     * @param string $nomCategorie
     * @return self
     */
    public function setNomCategorie(string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;

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
}
