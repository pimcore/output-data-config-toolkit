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

namespace OutputDataConfigToolkitBundle;

use OutputDataConfigToolkitBundle\Tools\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class OutputDataConfigToolkitBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;
    use PackageVersionTrait;

    protected function getComposerPackageName(): string
    {
        return 'pimcore/output-data-config-toolkit-bundle';
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
