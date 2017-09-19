<?php
declare (strict_types=1);

namespace Tardigrades\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SectionFieldExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator([
                __DIR__.'/../config/service'
            ])
        );

        $loader->load('commands.yml');
        $loader->load('controllers.yml');
        $loader->load('services.yml');
    }
}
