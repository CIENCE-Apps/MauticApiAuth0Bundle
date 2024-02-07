<?php


namespace Cinece\MauticApiAuth0Bundle;

use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;
use Cinece\MauticApiAuth0Bundle\DependencyInjection\Auth0Extension;
use Cinece\MauticApiAuth0Bundle\DependencyInjection\Security\Factory\Auth0Factory;
use Cinece\MauticApiAuth0Bundle\DependencyInjection\Compiler\Auth0CompilerPass;


class MauticApiAuth0Bundle extends Bundle
{
        /**
     * @example '2.1.0'
     * @var string
     */
    private $kernelVersion;

    public function __construct()
    {
        $this->kernelVersion = Kernel::VERSION;
        $this->extension = new Auth0Extension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (version_compare($this->kernelVersion, '2.1', '>=')) {
            /** @var SecurityExtension $extension */
            $extension = $container->getExtension('security');
            $extension->addSecurityListenerFactory(new Auth0Factory());
        }
        
        $container->addCompilerPass(new Auth0CompilerPass());

    }
}
