<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;
 
class Group extends AbstractOperator {

    public function getLabeledValue(\Object_Abstract $object) {
        $valueArray = array();

        $childs = $this->getChilds();
        foreach($childs as $c) {

            $value = $c->getLabeledValue($object);
            if(!empty($value) && !$value->empty) {
                $valueArray[] = $value;
            }

        }

        if(!empty($valueArray)) {
            return $valueArray;
        }
        return null;
    }
}
