<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\Livre;

final class LivreContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        //Ici je récupère le context (donc toute les annotations) et regarde de quelle entity on traite
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
        //DANS LE CAS D'UN GET -> NORMALIZATION
        //Si on traite de l'entityLivre, Et qu'il y a le context groups, et que je suis de role manager Et que c'est une normalisation
        if ($resourceClass === Livre::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_MANAGER') && true === $normalization) {
            //Alors je r'ajoute le groups get_role_manager au tableau de groups par défaut.
            //On pourrais aussi imaginer que j'en enlève..
            $context['groups'][] = 'get_role_manager';
        }
        //DANS LE CAS OU L'UTILISATEUR EST UN ADMIN EST QUE LA METHODE EST PUT -> DENORMALIZATION
        if ($resourceClass === Livre::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && false === $normalization) {
            if($request->getMethod()=="PUT"){
                //Alors je r'ajoute le groups put_admin au tableau de groups par défaut.
                //On pourrais aussi imaginer que j'en enlève..
                $context['groups'][] = 'put_admin';
            }
        }
        return $context;
    }
}