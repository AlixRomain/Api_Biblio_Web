<?php

namespace App\DataFixtures;

use App\Entity\Adherent;
use App\Entity\Livre;
use App\Entity\Pret;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    //NOTICE
    //POUR LANCER LES FIXTURES SANS SUPPRIMER LA BDD symfony console doctrine:fixtures:load --append
    //Mais ça nécessite que les tables que vous voulez remplir soit vide.
    //POUR LANCER LES FIXTURES ET SUPPRIMER LA BDD symfony console doctrine:fixtures:load
    private $entityManager;
    private $managerLivre;
    private $faker;
    private $passwordEncoder;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->managerLivre = $entityManager->getRepository(Livre::class);
        $this->faker = Factory::create("fr_FR");
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $entityManager)
    {

        $this->loadAdherent();
        $this->loadPret();

        $entityManager->flush();
    }
    //Création de 25 adhérents
    public function loadAdherent(){
        //Pour 25 adherent je persist 25 objet aléatoirement via faker
        for($i = 0; $i<25; $i++){
            $adherent = new Adherent();
            $adherent   ->setNom($this->faker->lastName())
                        ->setPrenom($this->faker->firstName($genre=mt_rand(0,1)))
                        ->setAdresse($this->faker->streetAddress())
                        ->setTel($this->faker->phoneNumber())
                        ->setCodePostal($genre= mt_rand(25410,39856))
                        ->setMail(strtolower($adherent->getNom())."@gmail.com")
                        ->setPassword($this->passwordEncoder->encodePassword($adherent,$adherent->getNom()));
            //La méthode permet d'attribuer une ckef/réference à chaque valeur/objet.
            //// Une sorte de tableau d'association connu gere par faker
            /// "adherentX" est associé à l'objet $adherentX
            $this->addReference("adherent".$i, $adherent);
            $this->entityManager->persist($adherent);
        }
        //je persist un 26 eme adherent qui sera l'admin
        //L65 je fait appel à une constante de Classe via le 'nom classe'::''nom de la constante'
        $adherentAdmin = new Adherent();
        $roleAdmin[]= ADHERENT::ROLE_ADMIN;
        $adherentAdmin      ->setNom("Alix")
                            ->setPrenom("Romain")
                            ->setMail("toto@toto.com")
                            ->setPassword($this->passwordEncoder->encodePassword($adherentAdmin,'toto'))
                            ->setRoles($roleAdmin);
        $this->entityManager->persist($adherentAdmin);

        $adherent = new Adherent();
        $role[]= ADHERENT::ROLE_MANAGER;
        $adherent   ->setNom("tata")
                    ->setPrenom("tata")
                    ->setMail("tata@tata.com")
                    ->setPassword($this->passwordEncoder->encodePassword($adherent,'tata'))
                    ->setRoles($role);
        $this->entityManager->persist($adherent);
    }


    //Création des prêts

    public function loadPret(){
        for($i = 0; $i<25; $i++){
            //Pour 1 a 5 pret de livre par adhérent soit 25 tour de boucles
            $max = mt_rand(1,5);
            for($j=0; $j<=$max; $j++){

                $livre = $this->managerLivre->findOneById($id = mt_rand(1,49));
                $livre->setDispo(1);
                $this->entityManager->persist($livre);
                $pret = new Pret();

                $pret->setLivre($livre)
                    //faker récupère l'objet $adhérent associé à la clef "adherent".$i
                     ->setAdherent($this->getReference("adherent".$i))
                     ->setDatePret($this->faker->dateTimeBetween('-6 months'));
                //Creation de la date de retour prévue en 3 étapes
                //1] Je transforme en timesStamp la date de près plus 15 Jours avec srttotime()
                //2] Je reformate la date en string avec date()
                //3] Je reformate en dateTime avec \DateTime::createFromFormat
                    $dateRpFormater= date('Y-m-d H:m:n',strtotime('+ 15 days', $pret->getDatePret()->getTimestamp()));
                    $dateRetourPrevue=\DateTime::createFromFormat('Y-m-d H:m:n', $dateRpFormater);
                $pret->setDateRetourPrevue($dateRetourPrevue);

                //je veux 1X sur 3 on simmule un retour de date réelle de pret d'un livre
                if(mt_rand(1,3) == 1){
                    $pret->setDateRetourReelle($this->faker->dateTimeInInterval($pret->getDatePret(), "+30 days"));
                }
                $this->entityManager->persist($pret);

            }
        }
    }
}
