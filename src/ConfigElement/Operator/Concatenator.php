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
