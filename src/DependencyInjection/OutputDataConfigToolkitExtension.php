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

use OutputDataConfigToolkitBundle\Controller\AdminController;
use OutputDataConfigToolkitBundle\Controller\ClassController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class OutputDataConfigToolkitExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $displayMode = $config['classification_store']['display_mode'];
        $defaultGrid = $config['tab_options']['default_classes'];
        $orderByName = $config['tab_options']['order_by_name'];

        $container
            ->getDefinition(ClassController::class)
            ->addMethodCall('setClassificationDisplayMode', [$displayMode]);

        $container
            ->getDefinition(AdminController::class)
            ->addMethodCall('setDefaultGridClasses', [$defaultGrid])
            ->addMethodCall('setOrderByName', [$orderByName]);
    }
}
