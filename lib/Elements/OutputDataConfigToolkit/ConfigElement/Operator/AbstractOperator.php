<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;

abstract class AbstractOperator extends ConfigElement\AbstractConfigElement {

    protected $childs;

    public function __construct($config, $context = null) {
        $this->label = $config->label;
        $this->childs = $config->childs;

        $this->context = $context;
    }

    public function getChilds() {
        return $this->childs;
    }
}
