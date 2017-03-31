<?php

namespace OutputDataConfigToolkitBundle;

use OutputDataConfigToolkitBundle\Tools\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class OutputDataConfigToolkitBundle extends AbstractPimcoreBundle
{
    /**
     * @inheritDoc
     */
    public function getCssPaths()
    {
        return [
            '/bundles/outputdataconfigtoolkit/css/admin.css'
        ];
    }
    
    public function getJsPaths()
    {
        return [
            '/bundles/outputdataconfigtoolkit/js/Plugin.js',
            '/bundles/outputdataconfigtoolkit/js/OutputDataConfigTab.js',
            '/bundles/outputdataconfigtoolkit/js/OutputDataConfigDialog.js',
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
            '/bundles/outputdataconfigtoolkit/js/outputDataConfigElements/operator/TextAddon.js'
        ];
    }

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return new Installer();
    }

}
