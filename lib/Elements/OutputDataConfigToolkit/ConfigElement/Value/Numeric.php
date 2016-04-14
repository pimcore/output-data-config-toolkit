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
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace Elements\OutputDataConfigToolkit\ConfigElement\Value;

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
            $labeledValue->value = \Zend_Locale_Format::toNumber($labeledValue->value, array("precision" => $this->precision ? $this->precision : null, "locale" => \Zend_Registry::get("Zend_Locale")));
        }

        return $labeledValue;

    }
}

