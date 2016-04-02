<?php

class Website_View_Helper_ProductListSpecification extends Zend_View_Helper_Abstract {

    public function productListSpecification($property, $product) {

        if($property instanceof Elements\OutputDataConfigToolkit\ConfigElement\Operator\Group) {
            return "NOT SUPPORTED";
        } else if($property instanceof Elements\OutputDataConfigToolkit\ConfigElement\Value\DefaultValue ||
            $property instanceof Elements\OutputDataConfigToolkit\ConfigElement\Operator\Concatenator) {
            $labeledValue = $property->getLabeledValue($product);
            if($labeledValue->def instanceof \Pimcore\Model\Object\ClassDefinition\Data\Select) {
                $value = $this->getSelectValue($labeledValue->def, $labeledValue->value);

            } else if($labeledValue->def instanceof \Pimcore\Model\Object\ClassDefinition\Data\Multiselect) {

                $values = $labeledValue->value;
                $translatedValues = array();
                foreach($values as $value) {
                    $translatedValues[] = $this->getSelectValue($labeledValue->def, $value);
                }

                $value = implode(", ", $translatedValues);


            } else if($labeledValue->def instanceof \Pimcore\Model\Object\ClassDefinition\Data\Objects) {

                $names = array();
                if(is_array($labeledValue->value)) {
                    foreach($labeledValue->value as $entry) {
                        if($entry instanceof \Pimcore\Model\Object\AbstractObject && method_exists($entry, "getName")) {
                            $names[] = $entry->getName();
                        }
                    }
                }

                $value = implode(", ", $names);

            } else if($labeledValue->value instanceof \Pimcore\Model\Object\AbstractObject && method_exists($labeledValue->value, "getName")) {
                    $value = $labeledValue->value->getName();
            } else {
                $value = $labeledValue->value;
            }


            if(is_numeric($value)) {
                $value = Zend_Locale_Format::toNumber($value, array('locale'=>Zend_Registry::get('Zend_Locale')));
            }

            return $value;

        } else {
            p_r($property);
        }

    }


    private function getSelectValue($def, $value) {
        if($value) {
            return $this->view->translate("optionvalue." . $value);
        }
    }
}
