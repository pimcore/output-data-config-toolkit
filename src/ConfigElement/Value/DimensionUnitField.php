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

class DimensionUnitField extends DefaultValue
{
    const RAW_RESULT = '1';

    const ONLY_VALUE = '2';

    const VALUE_WITH_UNIT = '3';

    protected $mode = self::RAW_RESULT;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        if ($config->mode) {
            $this->mode = $config->mode;
        }
    }

    public function getLabeledValue($object)
    {
        $rawResult = parent::getLabeledValue($object);
        if ($this->mode == self::RAW_RESULT) {
            return $rawResult;
        } elseif (!empty($rawResult)) {
            $result = new \stdClass();
            $result->label = $rawResult->label;
            $result->def = $rawResult->def ?? null;
            $result->value = null;

            $formatter = \Pimcore::getContainer()->get(\Pimcore\Localization\IntlFormatter::class);

            if (!empty($rawResult->value)) {
                if ($this->mode == self::ONLY_VALUE) {
                    $value = $rawResult->value->getValue();
                    if (is_numeric($value)) {
                        $value = $formatter->formatNumber($value);
                    }
                    $result->value = $value;
                } else {
                    $value = $rawResult->value->getValue();
                    if (is_numeric($value)) {
                        $value = $formatter->formatNumber($value);
                    }
                    $result->value = $value . ($rawResult->value->getUnit() ? ' ' . $rawResult->value->getUnit()->getAbbreviation() : '');
                }
            }

            if (empty($result->value)) {
                $result->empty = true;
            } else {
                $result->empty = false;
            }

            return $result;
        }

        return null;
    }
}
