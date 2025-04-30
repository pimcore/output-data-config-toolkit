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

namespace OutputDataConfigToolkitBundle\ConfigElement\Value;

class Numeric extends DefaultValue
{
    protected $precision;

    protected $formatNumber;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        $this->formatNumber = ($config->formatNumber ?? null);
        $this->precision = ($config->precision ?? null);
    }

    public function getLabeledValue($object)
    {
        $labeledValue = parent::getLabeledValue($object);

        if ($labeledValue === null) {
            return null;
        }

        if ($this->precision) {
            $labeledValue->value = round($labeledValue->value, $this->precision);
        }

        if ($this->formatNumber) {
            $formatter = \Pimcore::getContainer()->get(\Pimcore\Localization\IntlFormatter::class);

            if (!$labeledValue->empty) {
                //TODO consider precision
                $labeledValue->value = $formatter->formatNumber((float)$labeledValue->value);
            }
        }

        return $labeledValue;
    }
}
