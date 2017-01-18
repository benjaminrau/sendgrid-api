<?php

namespace Ins\SendGridBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SendGridExtension extends Extension
{
	/**
	 * {@inheritDoc}
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
		$processedConfiguration = $this->processConfiguration($configuration, $configs);

		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yml');

		$mailerServiceDefinition = $container->getDefinition('service.send_grid_mailer');
		$mailerServiceDefinition->addMethodCall('setConfiguration', array($processedConfiguration));

        $container->setParameter('send_grid.apikey', $processedConfiguration['apikey']);
        $container->setParameter('send_grid.disable_delivery',$processedConfiguration['disable_delivery']);
        $container->setParameter('send_grid.patterns',$processedConfiguration['patterns']);
        $container->setParameter('send_grid.enable_restriction',$processedConfiguration['enable_restriction']);
        $container->setParameter('send_grid.delivery_address',$processedConfiguration['delivery_address']);
	}
}
