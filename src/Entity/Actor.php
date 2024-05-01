<?php

namespace App\Entity;

use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[ORM\Table(name: 'actor')]
class Actor
{

    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;


    #[ORM\Column(name: 'nom', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please provide a name.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Name cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Type(
        type: 'string',
        message: 'Name must be a string.'
    )]
    #[Assert\NotNull(message: 'Name cannot be null.')]
    #[Assert\Regex(
        pattern: "/^[A-Z][a-zA-Z0-9\s]*$/",
        message: 'The actor name must start with an uppercase letter .'
    )]
    private string $nom;


    #[ORM\Column(name: 'image', type: 'text', length: 0, nullable: false)]
    private string $image;


    #[ORM\Column(name: 'biographie', type: 'text', length: 0, nullable: false)]
    #[Assert\NotBlank(message: 'Please provide a biography.')]
    #[Assert\Type(
        type: 'string',
        message: 'Biography must be a string.'
    )]
    #[Assert\NotNull(message: 'Biography cannot be null.')]
    private string $biographie;

    #[ORM\ManyToMany(targetEntity: Film::class, mappedBy: 'actors')]
    private Collection $films;

    public function __construct()
    {
        $this->films = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    /**
     * @return Collection<int, Film>
     */
    public function getFilms(): Collection
    {
        return $this->films;
    }

    public function addFilm(Film $film): static
    {
        if (!$this->films->contains($film)) {
            $this->films->add($film);
            $film->addActor($this);
        }

        return $this;
    }

    public function removeFilm(Film $film): static
    {
        if ($this->films->removeElement($film)) {
            $film->removeActor($this);
        }

        return $this;
    }


}
