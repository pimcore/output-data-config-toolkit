<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;
 
class Table extends AbstractOperator {

    protected $tooltip;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);
        $this->tooltip = $config->tooltip;
    }

    public function getLabeledValue($object) {
        $value = new \stdClass();

        $childs = $this->getChilds();

        $value->label = $this->label;
        $value->type = "Operator_Table";

        $isEmpty = false;
        foreach($childs as $c) {
            $row = $c->getLabeledValue($object);
            $valueArray[] = $row;
            $isEmpty = $row->empty;
        }
        $value->empty = $isEmpty;
        $value->value = $valueArray;
        $value->tooltip = $this->tooltip;
        return $value;
    }

    public function getTooltip() {
        return $this->tooltip;
    }

}
