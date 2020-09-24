<?php

namespace Leon\BswBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LeonBswExtension extends Extension
{
    /**
     * Build the extension services
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');

        $ymlLoader = new YamlFileLoader($container, $locator);
        $ymlLoader->load('bsw.yaml');
    }
}