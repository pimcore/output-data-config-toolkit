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

class Concatenator extends AbstractOperator
{
    protected $glue;

    protected $forceValue;

    protected $formatNumbers;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);
        $this->glue = $config->glue;
        $this->forceValue = $config->forceValue ?? false;
        $this->formatNumbers = $config->formatNumbers ?? false;
    }

    public function getLabeledValue($object)
    {
        $result = new \stdClass();
        $result->value = null;
        $result->label = $this->label;

        $hasValue = true;
        if (!$this->forceValue) {
            $hasValue = false;
        }

        $childs = $this->getChilds();
        $valueArray = [];

        foreach ($childs as $c) {
            if ($c instanceof AbstractConfigElement) {
                $c->setClassificationstore($this->getClassificationstore());
                $c->setClassificationstoreGroup($this->getClassificationstoreGroup());
            }

            $value = $c->getLabeledValue($object) ? $c->getLabeledValue($object)->value : null;

            if (!$hasValue) {
                if (is_object($value) && method_exists($value, 'isEmpty')) {
                    $hasValue = !$value->isEmpty();
                } else {
                    $hasValue = !empty($value);
                }
            }

            if ($this->formatNumbers && is_numeric($value)) {
                $formattingService = \Pimcore::getContainer()->get(\Pimcore\Localization\IntlFormatter::class);
                $value = $formattingService->formatNumber($value);
            }

            if ($value !== null) {
                $valueArray[] = $value;
            }
        }

        if ($hasValue) {
            $result->value = implode($this->glue, $valueArray);

            return $result;
        } else {
            $result->empty = true;

            return $result;
        }
    }
}
