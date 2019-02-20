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
                            ->info("possible values are [all, object, relevant, none]")
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
                ->arrayNode("tab_options")
                    ->children()
                        ->booleanNode("order_by_name")->defaultFalse()->end()
                        ->arrayNode("default_classes")
                            ->info("list of class names or ids")
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
