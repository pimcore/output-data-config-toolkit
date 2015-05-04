<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;
 
class TableRow extends AbstractOperator {

    protected $headline;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);

        $this->headline = $config->headline;
    }

    public function getLabeledValue(\Object_Abstract $object) {
        $value = new \stdClass();

        $isEmpty = true;
        $childs = $this->getChilds();

        foreach($childs as $c) {

            $col = $c->getLabeledValue($object);
            if(!empty($col) && (!$col->empty && !($c instanceof \Elements\OutputDataConfigToolkit\ConfigElement\Operator\Text))) {
                $isEmpty = false;
            }
            $valueArray[] = $c->getLabeledValue($object);
        }

        $value->value = $valueArray;
        $value->headline = $this->headline;


        if($isEmpty) {
            $value->empty = true;
        } else {
            $value->empty = false;
        }

        return $value;
    }
}
