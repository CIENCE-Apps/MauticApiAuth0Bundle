<?php

namespace Cinece\MauticApiAuth0Bundle\Security\EntryPoint;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\AuthenticationException as Auth0AuthenticationException;

class Auth0EntryPoint implements AuthenticationEntryPointInterface
{
    protected $auth0Service;

    public function __construct(Auth0 $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {        
        $exception = new Auth0AuthenticationException();
        
        return $exception->getHttpResponse();
    }
}