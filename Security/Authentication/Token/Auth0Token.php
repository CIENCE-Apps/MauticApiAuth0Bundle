<?php

namespace Cinece\MauticApiAuth0Bundle\Security\Authentication\Token;


use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Auth0\SDK\Token;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;


class Auth0Token extends AbstractToken
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var []
     */
    protected $auth0Token;

    
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setAuth0Token($auth0Token)
    {
        $this->auth0Token = $auth0Token;
    }

    public function getAuth0Token()
    {
        return $this->auth0Token;
    }


    public function getCredentials()
    {        
        return $this->token;
    }
}
