<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace OutputDataConfigToolkitBundle\OutputDefinition;

class Listing extends \Pimcore\Model\Listing\AbstractListing {

    /**
     * @var array
     */
    public $outputDefinitions;

    /**
     * @var array
     */
    public function isValidOrderKey($key) {
        if($key == "o_id" || $key == "o_classId" || $key == "channel") {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    function getOutputDefinitions() {
        if(empty($this->outputDefinitions)) {
            $this->load();
        }
        return $this->outputDefinitions;
    }

    /**
     * @param array $units
     * @return void
     */
    function setOutputDefinitions($outputDefinitions) {
        $this->outputDefinitions = $outputDefinitions;
    }

}
