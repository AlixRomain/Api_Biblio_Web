<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\AdherentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @UniqueEntity(
 *     fields={"mail"},
 *     message="L'adresse mail {{ value }} est déjà inscrit en base"
 * )
 *
 * @ORM\Entity(repositoryClass=AdherentRepository::class)
 * @ApiResource(
 *      collectionOperations={
 *          "get"={
 *             "method" = "GET",
 *             "path"="/adherent",
 *             "security" = " is_granted('ROLE_MANAGER')",
 *             "security_message" = "Vous n'avez pas les droits pour effectuer cette opération.'"
 *           },
 *          "post"={
 *             "method" = "POST",
 *             "path"="/adherent",
 *             "security" = " is_granted('ROLE_MANAGER')",
 *             "security_message" = "Vous n'avez pas les droits pour effectuer cette opération.'",
 *              "denormalization_context"={
 *                  "groups"= {"post_manager"}
 *              }
 *           },
 *          "statNbPretsParAdherent"={
 *              "method" ="GET",
 *              "route_name"="adherents_nbPrets",
 *              "controller"=StatsController::class
 *          }
 *     },
 *     itemOperations={
 *          "get" ={
 *              "method" ="GET",
 *              "path" = "/adherent/{id}",
 *              "security" = "(is_granted('ROLE_ADHERENT') and object == user ) or is_granted('ROLE_MANAGER')",
 *              "security_message" = "Vous ne pouvez avoir accès qu'à votre profil.",
 *              "normalization_context"={
 *                  "groups"= {"get_adherent"}
 *              }
 *          },
 *          "getNbPrets"={
 *              "method" ="GET",
 *              "route_name"="adherent_prets_count"
 *          },
 *          "put" ={
 *              "method" ="put",
 *              "path" = "/adherent/{id}",
 *              "security" = "(is_granted('ROLE_ADHERENT') and object == user ) or is_granted('ROLE_MANAGER')",
 *              "security_message" = "Vous ne pouvez avoir accès quà votre propre profil.",
 *              "denormalization_context"={
 *                  "groups"= {"put_manager", "put_adherent"}
 *              }
 *          },
 *          "delete"={
 *             "method" = "DELETE",
 *             "path" = "/adherent/{id}",
 *             "security"= "is_granted('ROLE_ADMIN')",
 *             "security_message" = "Seul l'admin peut effectuer cette action.'"
 *          }
 *     }
 * )
 */
//Quand on implémente une classe on doit rappatrié toute ces méthodes!!
//Implémenté revient un peut à se cloner une classe sur une autre. Ici Adhérent à ses méthodes plus celles de UserInterface
class Adherent implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_ADHERENT = 'ROLE_ADHERENT';
    const ROLE_DEFAULT = 'ROLE_DEFAULT';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_adherent","put_adherent","post_manager"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_adherent","put_adherent","post_manager"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_adherent","put_adherent","post_manager"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_adherent","put_adherent","post_manager"})
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"put_manager"})
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_adherent","put_adherent","post_manager"})
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"put_adherent","post_manager"})
     */
    private $password;
    /**
     * @ORM\Column(type="array", length=255, nullable=true)
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=Pret::class, mappedBy="adherent")
     * @ApiSubresource()
     * @Groups({"get_adherent"})
     */
    private $prets;

    public function __construct()
    {
        $this->prets = new ArrayCollection();
        $role[] = self::ROLE_DEFAULT;
        $this->roles = $role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
            $pret->setAdherent($this);
        }

        return $this;
    }

    public function removePret(Pret $pret): self
    {
        if ($this->prets->removeElement($pret)) {
            // set the owning side to null (unless already changed)
            if ($pret->getAdherent() === $this) {
                $pret->setAdherent(null);
            }
        }

        return $this;
    }

    /**
     * @return (Role|string)[]
     */
    public function getRoles() : array
    {
        return $this->roles;
    }

    /**
     * Affecte les roles de l'utilisateur
     * @param mixed $roles
     * @return self
     */
    public function setRoles(array $roles) : self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt()
    {
        return null;
    }
    //Pour moi le get USER name c'est le mail. Car je veux être authentifié par le mail
    public function getUsername()
    {
       return $this->getMail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}