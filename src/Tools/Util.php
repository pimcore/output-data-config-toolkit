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

namespace OutputDataConfigToolkitBundle\Tools;

/**
 * Class Util
 *
 * @package OutputDataConfigToolkitBundle\Tools
 */
class Util
{
    /**
     * @param string $class
     *
     * @return string
     */
    public static function getClassName(string $class): string
    {
        return substr(strrchr($class, '\\'), 1);
    }
}
