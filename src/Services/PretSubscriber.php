<?php
namespace App\Services;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Pret;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PretSubscriber implements EventSubscriberInterface
{
    private $token;
    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public static function getSubscribedEvents()
    {
        //je demande à ma méthode VIEW des écouteurs d'évenement de me jouer la fonction "getAuthenticatedUser"
        //à chaque fois que tu t'apprête à écrir quelques chose dans la BDD (entre persist() et flush())
        return [
          kernelEvents::VIEW =>['getAuthenticatedUser', EventPriorities::PRE_WRITE]
        ];
    }
    //View event me permet d'avoir l'évenement qui a déclenché
    public function getAuthenticatedUser(ViewEvent $event)
    {
        $entity = $event->getControllerResult();//récupère l'entity qui a déclenche l'évènement
        $method = $event->getRequest()->getMethod();//récupére la method PUT/DELETE/POST/GET
        //Ici on récupère le user deouis le payload du token. Astucieux^^
        $adherent = $this->token->getToken()->getUser();//récupère l'adhérent actuellemnt connecté avec son token

        //Si l'entité est une instance de l'entity Pret, et que la méthode est POST
        if($entity instanceof Pret){
            if($method == Request::METHOD_POST){
                $entity->setAdherent($adherent);//on inscrit le user comme Adhérent dans la propriée de l'entity Pret
            }elseif($method == Request::METHOD_PUT){
                if($entity->getDateRetourReelle() == null){
                    $entity->getLivre()->setDispo(false);
                }else{
                    $entity->getLivre()->setDispo(true);
                }
            }elseif($method == Request::METHOD_DELETE){
                $entity->getLivre()->setDispo(true);
            }
        }
    }
}