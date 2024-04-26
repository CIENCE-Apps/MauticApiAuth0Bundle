<?php

namespace Cinece\MauticApiAuth0Bundle\Security\Firewall;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Cinece\MauticApiAuth0Bundle\DependencyInjection\Auth0Wrapper\Wrapper;
use Cinece\MauticApiAuth0Bundle\Security\Authentication\Token\Auth0Token;

class Auth0Listener implements ListenerInterface
{

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var Wrapper
     */
    protected $auth0Clients;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, Wrapper $auth0Clients)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->auth0Clients = $auth0Clients;
    }


    /**
     * @param GetResponseEvent $event The event.
     */
    public function handle(GetResponseEvent $event)
    {
        
        if (!$event->getRequest()->headers->has('Authorization')) {
            return;
        } 

        $hayStack['Authorization'] = $event->getRequest()->headers->get('Authorization');
        
        /**
         * This will check if the token is valid on auth0
         * If it is not valid, it will throw an exception
         */
        $valid = false;
        foreach ($this->auth0Clients->getAuth0Clients() as $client) {
            if (null === $authToken = $client->getBearerToken(null, null, null, $hayStack, ['Authorization'])) {
                $valid = false;
            }else{
                $valid = true;
                break;
            }
        }

        if(!$valid){
            return;
        }
        
        $token = new Auth0Token();
        $token->setAuth0Token($authToken->toArray());
        $token->setToken($this->processBearerToken($hayStack['Authorization']));

        try {            
            
            $returnValue = $this->authenticationManager->authenticate($token);
            
            if($returnValue->isAuthenticated()){
                return $this->tokenStorage->setToken($returnValue);
            }

            if ($returnValue instanceof Response) {
                return $event->setResponse($returnValue);
            }
        } catch (AuthenticationException $e) {                        
            if (null !== $p = $e->getPrevious()) {
                $event->setResponse($p->getHttpResponse());
            }
        } 
    }

    private function processBearerToken($token){
        $token = trim($token);
        $token = 'Bearer ' === mb_substr($token, 0, 7) ? trim(mb_substr($token, 7)) : $token;
        return $token;
    }    
}
