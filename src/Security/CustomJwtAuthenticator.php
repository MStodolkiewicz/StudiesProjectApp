<?php

namespace App\Security;

use ApiPlatform\Core\Api\Entrypoint;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CustomJwtAuthenticator extends JWTAuthenticator implements AuthenticationEntryPointInterface
{


    public function __construct(JWTTokenManagerInterface $jwtManager, EventDispatcherInterface $eventDispatcher, TokenExtractorInterface $tokenExtractor, UserProviderInterface $userProvider)
    {
        parent::__construct($jwtManager, $eventDispatcher, $tokenExtractor, $userProvider);
    }


    public function supports(Request $request): ?bool
    {
        return false !== $this->getTokenExtractor()->extract($request);
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        //Workaround for not working entrypoint publication on /api endpoint
        return new Response(json_encode((object)[
            "@context" => "/api/contexts/Entrypoint",
            "@id" => "/api",
            "@type" => "Entrypoint",
            "category" => "/api/categories",
            "ingredient" => "/api/ingredients",
            "intake" => "/api/intakes",
            "product" => "/api/products",
            "rate" => "/api/rates",
            "subCategory" => "/api/sub_categories",
            "user" => "/api/users"
        ]));
//        return null;
    }


}