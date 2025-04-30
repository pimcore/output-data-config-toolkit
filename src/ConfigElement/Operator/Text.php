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

class Text extends AbstractOperator
{
    protected $textValue;

    public function __construct($config, $context = null)
    {
        $this->textValue = $config->textValue;
        $this->label = $config->label;

        $this->context = $context;
    }

    public function getLabeledValue($object)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        $result->value = $this->textValue;

        return $result;
    }
}
