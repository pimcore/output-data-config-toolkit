<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace OutputDataConfigToolkitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('output_data_config_toolkit');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('classification_store')
                    ->children()
                        ->enumNode('display_mode')
                            ->info('possible values are [all, object, relevant, none]')
                            ->values([
                                'all',          // always show all keys
                                'object',       // only show keys which are in any assigned group
                                'relevant',     // use 'object' mode if any group is assigned, else show all keys
                                'none'          // do not show classification store keys
                            ])
                            ->defaultValue('relevant')
                        ->end()
                        ->booleanNode('grouped')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('tab_options')
                    ->children()
                        ->booleanNode('order_by_name')->defaultFalse()->end()
                        ->arrayNode('default_classes')
                            ->info('list of class names or ids')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
