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

namespace OutputDataConfigToolkitBundle\OutputDefinition;

use OutputDataConfigToolkitBundle\OutputDefinition;

/**
 * Class Listing
 *
 * @package OutputDataConfigToolkitBundle\OutputDefinition
 *
 * @method OutputDefinition[] load()
 */
class Listing extends \Pimcore\Model\Listing\AbstractListing
{
    /**
     * @var array
     */
    public $outputDefinitions;

    /**
     * @param string $key
     */
    public function isValidOrderKey($key): bool
    {
        if ($key == 'objectId' || $key == 'classId' || $key == 'channel') {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getOutputDefinitions()
    {
        if (empty($this->outputDefinitions)) {
            $this->load();
        }

        return $this->outputDefinitions;
    }

    /**
     * @param array $outputDefinitions
     *
     * @return void
     */
    public function setOutputDefinitions($outputDefinitions)
    {
        $this->outputDefinitions = $outputDefinitions;
    }
}
