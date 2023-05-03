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

namespace OutputDataConfigToolkitBundle\ConfigElement\Operator;

use OutputDataConfigToolkitBundle\ConfigElement\AbstractConfigElement;
use OutputDataConfigToolkitBundle\ConfigElement\IConfigElement;

abstract class AbstractOperator extends AbstractConfigElement
{
    /**
     * @var IConfigElement[]
     */
    protected $childs;

    /**
     * @return IConfigElement[]
     */
    public function getChilds()
    {
        return $this->childs;
    }

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);
        $this->childs = $config->childs ?? [];
    }
}
