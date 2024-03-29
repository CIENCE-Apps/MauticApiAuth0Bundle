<?php

namespace Cinece\MauticApiAuth0Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\Config\FileLocator;

class Auth0Extension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        // If the config is empty, do not load the bundle
        if(!$config){
            return;
        }
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (array('auth0', 'security') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setParameter('mautic.api.auth0.sdk.config', $config['sdk']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'mautic_api_auth0';
    }
}