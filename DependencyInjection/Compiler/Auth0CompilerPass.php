<?php

namespace Cinece\MauticApiAuth0Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use GuzzleHttp\Client;

class Auth0CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mautic_api_auth0.sdk.config')) {
            return;
        }

        $definition = $container->getDefinition('mautic_api_auth0.sdk.config');

        $definition->addMethodCall('setHttpClient', [new Reference(Client::class)]);
        
    }
}
