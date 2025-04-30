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

namespace OutputDataConfigToolkitBundle\ConfigAttribute\Value;

use OutputDataConfigToolkitBundle\ConfigAttribute\AbstractConfigAttributeValue;
use OutputDataConfigToolkitBundle\ConfigElement\Value\DefaultValue as DefaultValueElement;
use OutputDataConfigToolkitBundle\Tools\Util;

/**
 * Class DefaultValue
 *
 * @package OutputDataConfigToolkitBundle\ConfigAttribute\Value
 */
class DefaultValue extends AbstractConfigAttributeValue
{
    /**
     * @return $this
     */
    public function applyDefaults()
    {
        return $this
            ->setDataType('input')
            ->setType('value')
            ->setClass(Util::getClassName(DefaultValueElement::class));
    }
}
