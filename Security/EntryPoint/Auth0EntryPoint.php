<?php

namespace Cinece\MauticApiAuth0Bundle\Security\EntryPoint;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

class Auth0EntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, AuthenticationException $authException = null)
    {        
        return new Response(
            json_encode(['error' => $authException->getMessage()]),
            Response::HTTP_UNAUTHORIZED,
            []
        );            
    }
}