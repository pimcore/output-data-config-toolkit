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

namespace OutputDataConfigToolkitBundle\ConfigElement;

abstract class AbstractConfigElement implements IConfigElement
{
    protected $attribute;
    protected $label;

    protected $context;

    public function __construct($config, $context = null)
    {
        $this->attribute = $config->attribute ?? null;
        $this->label = $config->label;

        $this->context = $context;
    }

    public function getLabel()
    {
        return $this->label;
    }
}
