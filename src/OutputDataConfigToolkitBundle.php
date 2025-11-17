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

namespace OutputDataConfigToolkitBundle;

use OutputDataConfigToolkitBundle\DependencyInjection\OutputDataConfigToolkitExtension;
use OutputDataConfigToolkitBundle\Tools\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @deprecated version 6.2
 */
class OutputDataConfigToolkitBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;
    use PackageVersionTrait;

    public function __construct()
    {
        trigger_deprecation(
            'pimcore/output-data-config-toolkit-bundle',
            '6.2',
            'The OutputDataConfigToolkitBundle is deprecated and will be discontinued with Pimcore Studio.'
        );
    }

    protected function getComposerPackageName(): string
    {
        return 'pimcore/output-data-config-toolkit-bundle';
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return new OutputDataConfigToolkitExtension();
    }

    public function getCssPaths(): array
    {
        return [
            '/bundles/outputdataconfigtoolkit/css/admin.css'
        ];
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/outputdataconfigtoolkit/js/Bundle.js',
            '/bundles/outputdataconfigtoolkit/js/OutputDataConfigTab.js',
            '/bundles/outputdataconfigtoolkit/js/OutputDataConfigDialog.js',
            '/bundles/outputdataconfigtoolkit/js/ClassTree.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/Abstract.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/value/DefaultValue.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/value/DimensionUnitField.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/value/DimensionUnitFieldText.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/value/StructuredTable.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/value/KeyValue.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/value/Numeric.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/Text.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/Group.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/Concatenator.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/Table.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/TableRow.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/TableCol.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/TranslateValue.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/CellFormater.js',
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/TextAddon.js',
        ];
    }

    public function getInstaller(): Installer
    {
        return new Installer();
    }
}
