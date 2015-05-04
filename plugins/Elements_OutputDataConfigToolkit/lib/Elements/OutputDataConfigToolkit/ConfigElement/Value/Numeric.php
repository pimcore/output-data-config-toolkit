<?php
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

    public function getLabeledValue(\Object_Abstract $object) {

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

