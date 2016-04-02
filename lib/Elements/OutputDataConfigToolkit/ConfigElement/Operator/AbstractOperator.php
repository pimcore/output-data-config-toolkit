<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;

abstract class AbstractOperator extends ConfigElement\AbstractConfigElement {

    /**
     * @var ConfigElement\IConfigElement
     */
    protected $childs;

    public function __construct($config, $context = null) {
        $this->label = $config->label;
        $this->childs = $config->childs;

        $this->context = $context;
    }

    /**
     * @return ConfigElement\IConfigElement
     */
    public function getChilds() {
        return $this->childs;
    }
}
