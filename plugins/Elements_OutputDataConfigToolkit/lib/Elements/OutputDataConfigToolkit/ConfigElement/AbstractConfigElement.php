<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;

abstract class AbstractConfigElement implements IConfigElement {

    protected $attribute;
    protected $label;

    protected $context;

    public function __construct($config, $context = null) {
        $this->attribute = $config->attribute;
        $this->label = $config->label;

        $this->context = $context;
    }

    public function getLabel() {
        return $this->label;
    }

}
