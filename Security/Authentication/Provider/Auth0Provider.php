<?php

namespace Cinece\MauticApiAuth0Bundle\Security\Authentication\Provider;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Exception\Auth0Exception;
use Cinece\MauticApiAuth0Bundle\Security\Authentication\Token\Auth0Token;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Mautic\UserBundle\Entity\User;
use Mautic\UserBundle\Entity\Role;
use Mautic\UserBundle\Model\UserModel;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

class Auth0Provider implements AuthenticationProviderInterface
{

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;
    /**
     * @var Auth0
     */
    protected $auth0Service;
    
    /**
     * @var UserCheckerInterface
     */
    protected $userChecker;

    /**
     * @var UserModel
     */
    protected $userModel;

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    public function __construct(
        UserProviderInterface $userProvider, 
        Auth0 $auth0Service, 
        UserCheckerInterface $userChecker, 
        UserModel $userModel,
        CoreParametersHelper $coreParametersHelper
        )
    {
        $this->userProvider = $userProvider;
        $this->auth0Service = $auth0Service;
        $this->userChecker = $userChecker;
        $this->userModel = $userModel;        
        $this->coreParametersHelper = $coreParametersHelper;
    }

     /**
    
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }

        try{
            $tokenData = $token->getAuth0Token();
            $tokenString = $token->getToken();
            $adminRole = $this->userModel->getSystemAdministrator()->getRole();
            $authorized_machines = $this->coreParametersHelper->getParameter('auth0_authorized_machines');
            $authorized_machines =  is_array($authorized_machines) ? $authorized_machines : [];

            /**
             * Machine to Machine token is handled here
             */
            if(in_array($tokenData['azp'], $authorized_machines)){
                try{
                    $user = $this->userProvider->loadUserByUsername($tokenData['sub']);
                }catch(UsernameNotFoundException $e){
                    $user = $this->createUser($tokenData, $adminRole);
                }
                
                if(null !== $user){
                    try{
                        $this->userChecker->checkPreAuth($user);
                    }catch(AccountStatusException $e){
                        throw new AuthenticationException('Access Denied ' . $e->getMessage());
                    }
                    $token->setUser($user);                    
                }

                $token = new Auth0Token($user->getRoles());
                $token->setAuthenticated(true);
                $token->setAuth0Token($tokenData);
                $token->setToken($tokenString);

                if(null !== $user){
                    try {                        
                        $this->userChecker->checkPostAuth($user);                        
                    } catch (AccountStatusException $e) {
                        throw new AuthenticationException('Access Denied ' . $e->getMessage());
                    }
                    
                    $token->setUser($user);                     
                }
                
                return $token;
            }
        }catch(\Exception $e){
            throw new AuthenticationException('Auth0 authentication failed', 0, $e);
        }
                
        throw new AuthenticationException('Auth0 authentication failed');        
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {        
        return $token instanceof Auth0Token;
    }


    private function createUser($tokenData, $role)
    {        
        $user = new User();
        $user->setUsername($tokenData['sub']);
        $user->setEmail($tokenData['sub'] . '.cience.com');
        $user->setFirstName('Auth0');
        $user->setLastName('User');
        $user->setRole($role);
        return $this->userProvider->saveUser($user);
    }

    
}