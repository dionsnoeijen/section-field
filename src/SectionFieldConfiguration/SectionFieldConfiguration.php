<?php
declare (strict_types=1);

namespace Tardigrades\SectionFieldConfiguration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class SectionFieldConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('section_field');
        $rootNode->children()
            ->scalarNode('default_connection')
                ->defaultValue('default')
            ->end()
        ->end();

        return $treeBuilder;
    }
}
