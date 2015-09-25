<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;

class Concatenator extends AbstractOperator {

    protected $glue;
    protected $forceValue;
    protected $formatNumbers;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);
        $this->glue = $config->glue;
        $this->forceValue = $config->forceValue;
        $this->formatNumbers = $config->formatNumbers;
    }

    public function getLabeledValue(\Object_Abstract $object) {
        $result = new \stdClass();
        $result->label = $this->label;

        $hasValue = true;
        if(!$this->forceValue) {
            $hasValue = false;
        }


        $childs = $this->getChilds();
        foreach($childs as $c) {
            $value = $c->getLabeledValue($object)->value;

            if(!$hasValue) {
                if(!empty($value) || ((method_exists($value, "isEmpty") && !$value->isEmpty()))) {
                    $hasValue = true;
                }
            }

            if($this->formatNumbers && is_numeric($value)) {
                $value = \Zend_Locale_Format::toNumber($value, array("locale" => \Zend_Registry::get("Zend_Locale")) );
            }

            if($value !== null) {
                $valueArray[] = $value;
            }
        }

        if($hasValue) {
            $result->value = implode($this->glue, $valueArray);
            return $result;
        } else {
            $result->empty = true;
            return $result;
        }
    }
}
