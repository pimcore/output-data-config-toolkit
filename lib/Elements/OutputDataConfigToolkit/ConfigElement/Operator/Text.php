<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;
 
class Text extends AbstractOperator {

    protected $textValue;

    public function __construct($config, $context = null) {
        $this->textValue = $config->textValue;
        $this->label = $config->label;

        $this->context = $context;
    }

    public function getLabeledValue($object) {
        $result = new \stdClass();
        $result->label = $this->label;
        $result->value = $this->textValue;
        return $result;
    }
}
