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

namespace OutputDataConfigToolkitBundle\ConfigAttribute;

/**
 * Class AbstractConfigAttributeValue
 *
 * @package OutputDataConfigToolkitBundle\ConfigAttribute
 */
abstract class AbstractConfigAttributeOperator extends AbstractConfigAttribute
{
    /**
     * @return $this
     */
    public function applyDefaults()
    {
        return $this->setType('operator');
    }
}
