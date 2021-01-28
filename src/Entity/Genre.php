<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 * @UniqueEntity(
 *     fields={"libelle"},
 *     message="Le genre {{ value }} est déjà inscrit en base"
 * )
 * @ApiResource(
 *     attributes={
 *          "order"= {
 *              "libelle":"ASC",
 *           }
 *     }
 * )
 */
//CI-dessous paramétrer de manière a récupérer via 2 url avec le verbe GET et les données simples ou/et les full de l'objet Genre
//* @ApiResource(
// *      itemOperations={
//*          "get_simple"={
//*              "method"="GET",
//*              "path"="/genres/{id}/simple",
//*              "normalization_context"={"groups"={"listSerialiser"}}
//*          },
//*          "get_full"={
//*              "method"="GET",
//*              "path"="/genres/{id}/full",
//*              "normalization_context"={"groups"={"genreFullList"}}
//*           }
// *     },
// *     collectionOperations={"get"}
//*)
//CI-dessous paramétrer de manière a récupérer en 1 url les données simples ou FULL de genre (listSerialiser/genreFullList)

//* @ApiResource(
//*     itemOperation={"get"},
//*     collectionOperation={"get"},
//*     normalizationContext={
//*          "groups"={"genreFullList OU SI TU VEUX listSerialiser"}
//*     }
//* )
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"listSerialiser","genreFullList"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"listSerialiser","genreFullList", "auteurFullList"})
     * @Assert\Length(
     *     min= 2,
     *     max= 50,
     *     minMessage = "Le libellé doit contenit au moin {{ limit }} caractères.",
     *     maxMessage = "Le libellé doit contenit au plus {{ limit }} caractères."
     * )
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="genre")
     * @Groups({"genreFullList"})
     * @ApiSubresource()
     */
    private $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): self
    {
        if (!$this->livres->contains($livre)) {
            $this->livres[] = $livre;
            $livre->setGenre($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): self
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getGenre() === $this) {
                $livre->setGenre(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getLibelle();
    }
}
