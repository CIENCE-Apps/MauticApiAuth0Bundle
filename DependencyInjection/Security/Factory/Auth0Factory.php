<?php

namespace Cinece\MauticApiAuth0Bundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;


class Auth0Factory implements SecurityFactoryInterface
{

   /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.mautic_api_auth0.'.$id;
        if (class_exists(ChildDefinition::class)) {
            $definition = new ChildDefinition('mautic_api_auth0.security.authentication.provider');
        } else {
            $definition = new DefinitionDecorator('mautic_api_auth0.security.authentication.provider');
        }
        $container
            ->setDefinition($providerId, $definition)
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.mautic_api_auth0.'.$id;

        if (class_exists(ChildDefinition::class)) {
            $definition = new ChildDefinition('mautic_api_auth0.security.authentication.listener');
        } else {
            $definition = new DefinitionDecorator('mautic_api_auth0.security.authentication.listener');
        }
        
        $container->setDefinition($listenerId, $definition);
        
        return array($providerId, $listenerId, 'mautic_api_auth0.security.entry_point');
    }
    

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'mautic_api_auth0';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }    
    
}

