<?php

namespace OutputDataConfigToolkitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('output_data_config_toolkit');

        $rootNode
            ->children()
                ->arrayNode("classification_store")
                    ->children()
                        ->enumNode('display_mode')
                            ->values([
                                'all',          // always show all keys
                                'object',       // only show keys which are in any assigned group
                                'relevant',     // use 'object' mode if any group is assigned, else show all keys
                                'none'          // do not show classification store keys
                            ])
                            ->defaultValue('relevant')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('default_grid')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
