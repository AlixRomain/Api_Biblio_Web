<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *          "get_coll_roles_adherent"={
 *             "method" = "GET",
 *             "path"="/adherent/livres",
 *             "normalization_context"={
 *                  "groups"= {"get_role_adherent"}
 *              }
 *           },
 *          "get_coll_roles_manager"={
 *             "method" = "GET",
 *             "path" = "/manager/livres",
 *             "security"= "is_granted('ROLE_MANAGER')",
 *             "security_message"= "Vous n'avez pas les droits d'accéder à cette ressource"
 *           },
 *          "post"={
 *             "method" = "POST",
 *             "security"= "is_granted('ROLE_MANAGER')",
 *             "security_message"= "Vous n'avez pas les droits d'accéder à cette ressource"
 *           }
 *     },
 *     itemOperations={
 *         "get_item_roles_adherent"={
 *             "method" = "GET",
 *             "path"="/adherent/livre/{id}",
 *             "normalization_context"={
 *                  "groups"= {"get_role_adherent"}
 *              }
 *           },
 *          "get_item_roles_manager"={
 *             "method" = "GET",
 *             "path" = "/manager/livre/{id}",
 *             "security"= "is_granted('ROLE_MANAGER')",
 *             "security_message"= "Vous n'avez pas les droits d'accéder à cette ressource"
 *           },
 *          "put_item_roles_manager"={
 *             "method" = "PUT",
 *             "path" = "/manager/livre/{id}",
 *             "security"= "is_granted('ROLE_MANAGER')",
 *             "security_message"= "Vous n'avez pas les droits d'accéder à cette ressource",
 *             "denormalization_context"={
 *                  "groups"= {"put_manager"}
 *              }
 *           },
 *          "put_item_roles_admin"={
 *             "method" = "PUT",
 *             "path" = "/manager/livre/{id}",
 *             "security"= "is_granted('ROLE_ADMIN')",
 *             "security_message"= "Vous n'avez pas les droits d'accéder à cette ressource"
 *           },
 *           "delete"={
 *             "method" = "DELETE",
 *             "path" = "/manager/livre/{id}",
 *             "security"= "is_granted('ROLE_ADMIN')",
 *             "security_message"= "Vous n'avez pas les droits d'accéder à cette ressource"
 *           }
 *     },
 *     attributes={
 *          "order"= {
 *              "titre":"ASC",
 *              "prix":"DESC"
 *           }
 *     }
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "titre"  : "ipartial",
 *          "auteur" : "exact",
 *          "genre"  : "exact"
 *     }
 * )
 * @ApiFilter(
 *     RangeFilter::class,
 *     properties={
 *          "prix"
 *     }
 * )
 *  @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "livre",
 *           "prix",
 *           "auteur.nom"
 *     }
 * )
 * @ApiFilter(
 *     PropertyFilter::class,
 *     arguments={
 *          "parameterName" : "properties",
 *           "overrideDefaultProperties" : false,
 *           "whitelist" = {
 *                "isbn",
 *                "titre",
 *                "prix"
 *           }
 *     }
 * )
 */
class Livre_Caduc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"auteurFullList"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"genreFullList"})
     * @Groups({"auteurFullList","put_manager"})
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"genreFullList","get_role_adherent","put_manager"})
     * @Groups({"auteurFullList"})
     */
    private $titre;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"genreFullList"})
     * @Groups({"auteurFullList"})
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="livres")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"auteurFullList","get_role_adherent","put_manager"})
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity=Editeur::class, inversedBy="livres")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"genreFullList","get_role_adherent","put_manager"})
     * @Groups({"auteurFullList"})
     */
    private $editeur;

    /**
     * @ORM\ManyToOne(targetEntity=Auteur::class, inversedBy="livres")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"genreFullList","get_role_adherent","put_manager"})

     */
    private $auteur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"genreFullList","get_role_adherent","put_manager"})
     * @Groups({"auteurFullList"})
     */
    private $annee;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"genreFullList"})
     * @Groups({"auteurFullList","get_role_adherent","put_manager"})
     */
    private $langue;

    /**
     * @ORM\OneToMany(targetEntity=Pret::class, mappedBy="livre")
     */
    private $prets;

    public function __construct()
    {
        $this->prets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getEditeur(): ?Editeur
    {
        return $this->editeur;
    }

    public function setEditeur(?Editeur $editeur): self
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(?string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * @return Collection|Pret[]
     */
    public function getPrets(): Collection
    {
        return $this->prets;
    }

    public function addPret(Pret $pret): self
    {
        if (!$this->prets->contains($pret)) {
            $this->prets[] = $pret;
            $pret->setLivre($this);
        }

        return $this;
    }

    public function removePret(Pret $pret): self
    {
        if ($this->prets->removeElement($pret)) {
            // set the owning side to null (unless already changed)
            if ($pret->getLivre() === $this) {
                $pret->setLivre(null);
            }
        }

        return $this;
    }
}
