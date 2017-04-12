<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace OutputDataConfigToolkitBundle\ConfigElement\Value;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;

class Numeric extends DefaultValue {

    protected $precision;
    protected $formatNumber;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);

        $this->formatNumber = ($config->formatNumber ? $config->formatNumber : null);
        $this->precision = ($config->precision ? $config->precision : null);
    }

    public function getLabeledValue($object) {

        $labeledValue = parent::getLabeledValue($object);

        if($this->precision) {
            $labeledValue->value = round($labeledValue->value, $this->precision);
        }

        if($this->formatNumber) {
            $formatter = \Pimcore::getContainer()->get("pimcore.locale.intl_formatter");

            //TODO consider precision
            $labeledValue->value = $formatter->formatNumber($labeledValue->value);
        }

        return $labeledValue;

    }
}

