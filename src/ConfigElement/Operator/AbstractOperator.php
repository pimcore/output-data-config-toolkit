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
