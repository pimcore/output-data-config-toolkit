<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

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
