<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PretRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PretRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *             "method" = "GET",
 *             "path"="/pret",
 *             "security" = "(is_granted('ROLE_ADHERENT') and object.getAdherent() == user ) or is_granted('ROLE_MANAGER')"
 *           },
 *          "post"={
 *             "method" = "POST",
 *             "path"="/pret",
 *             "security_message" = "Vous ne pouvez pas empreinter un livre à la place d'un autre."
 *           }
 *     },
 *     itemOperations={
 *          "get" ={
 *              "method" ="GET",
 *              "path" = "/pret/{id}",
 *              "security" = "(is_granted('ROLE_ADHERENT') and object.getAdherent() == user ) or is_granted('ROLE_MANAGER')",
 *              "security_message" = "Vous ne pouvez avoir accès qu'à vos prêts."
 *          },
 *          "put" ={
 *              "method" ="put",
 *              "path" = "/pret/{id}",
 *              "security" = "is_granted('ROLE_MANAGER')",
 *              "security_message" = "Vous ne pouvez avoir accès quà vos prêts.",
 *              "denormalization_context"={
 *                  "groups"= {"put_manager"}
 *              }
 *          },
 *          "delete"={
 *             "method" = "DELETE",
 *             "path" = "/pret/{id}",
 *             "security"= "is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')"
 *          }
 *     }
 * )
 */
class Pret
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePret;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateRetourPrevue;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"put_manager"})
     */
    private $dateRetourReelle;

    /**
     * @ORM\ManyToOne(targetEntity=Livre::class, inversedBy="prets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livre;

    /**
     * @ORM\ManyToOne(targetEntity=Adherent::class, inversedBy="prets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $adherent;

    public function __construct()
    {
        $this->datePret   = new \DateTime();
        $dateRpFormater   = date('Y-m-d H:m:n',strtotime('+ 15 days', $this->getDatePret()->getTimestamp()));
        $dateRetourPrevue = \DateTime::createFromFormat('Y-m-d H:m:n', $dateRpFormater);
        $this->dateRetourPrevue = $dateRetourPrevue;
        $this->dateRetourReelle = null;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePret(): ?\DateTimeInterface
    {
        return $this->datePret;
    }

    public function setDatePret(\DateTimeInterface $datePret): self
    {
        $this->datePret = $datePret;

        return $this;
    }

    public function getDateRetourPrevue(): ?\DateTimeInterface
    {
        return $this->dateRetourPrevue;
    }

    public function setDateRetourPrevue(\DateTimeInterface $dateRetourPrevue): self
    {
        $this->dateRetourPrevue = $dateRetourPrevue;

        return $this;
    }

    public function getDateRetourReelle(): ?\DateTimeInterface
    {
        return $this->dateRetourReelle;
    }

    public function setDateRetourReelle(?\DateTimeInterface $dateRetourReelle): self
    {
        $this->dateRetourReelle = $dateRetourReelle;

        return $this;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): self
    {
        $this->livre = $livre;

        return $this;
    }

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }

    /**
     * Rend Indisponible le livre avant le persite d'un nouveau pret
     * @ORM\PrePersist()
     * @return void
     */

    public function RendIndispoLivre(){
        $this->getLivre()->setDispo(false);
    }

    /**
     * ATTENTION NE MARCHE PAS, PREUPDATE NE MARCHE QUE SUR L'OBJET DE L'ENTITY CONCERNER,
     * IL FAUDRA UTILISER UNE CLASSE SUBSCRIBER
     * Change l'etat de  la dispo d'un livre avant l'update d'un pret
     * @ORM\PreUpdate()
     * @return void
     */
    public function RendDispoLivreApresMaj(){
        if($this->dateRetourReelle == null){
            $this->getLivre()->setDispo(false);
        }else{
            $this->getLivre()->setDispo(true);
        }
    }
    /**
     * ATTENTION NE MARCHE PAS, PREREMOVE NE MARCHE QUE SUR L'OBJET DE L'ENTITY CONCERNER,
     * IL FAUDRA UTILISER UNE CLASSE SUBSCRIBER
     * Rend dispo un livre avant la suppression d'un d'un pret
     * @ORM\PreRemove()
     * @return void
     */
    public function RendDispoLivreApresSuppression(){
            $this->getLivre()->setDispo(true);
    }
}
