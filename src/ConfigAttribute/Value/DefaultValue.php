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
