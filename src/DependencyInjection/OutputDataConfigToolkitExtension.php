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
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $displayMode = $config['classification_store']['display_mode'];
        $grouped = $config['classification_store']['grouped'];
        $defaultGrid = $config['tab_options']['default_classes'];
        $orderByName = $config['tab_options']['order_by_name'];

        $container
            ->getDefinition(ClassController::class)
            ->addMethodCall('setClassificationDisplayMode', [$displayMode])
            ->addMethodCall('setClassificationGroupedDisplay', [$grouped]);

        $container
            ->getDefinition(AdminController::class)
            ->addMethodCall('setDefaultGridClasses', [$defaultGrid])
            ->addMethodCall('setOrderByName', [$orderByName]);
    }
}
