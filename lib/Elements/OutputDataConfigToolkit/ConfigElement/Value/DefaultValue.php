<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Value;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;

class DefaultValue extends ConfigElement\AbstractConfigElement {

    public function getLabeledValue(\Object_Abstract $object) {

        $attributeParts = explode("~", $this->attribute);
        $label = $this->label;

        $getter = "get" . ucfirst($this->attribute);

        if (substr($this->attribute, 0, 1) == "~") {
            // key value, ignore for now
        } else if(count($attributeParts) > 1) {
            $brickType = $attributeParts[0];
            $brickKey = $attributeParts[1];

            $getter = "get" . ucfirst(\Object_Service::getFieldForBrickType($object->getClass(), $brickType));
            $brickTypeGetter = "get" . ucfirst($brickType);
            $brickGetter = "get" . ucfirst($brickKey);
        }
        if(method_exists($object, $getter)) {
            $value = $object->$getter();

            $def = $object->getClass()->getFieldDefinition($this->attribute);
            if(empty($label)) {
                if($def) {
                    $label =  $def->getTitle();
                }
            }

            if(!empty($value) && !empty($brickGetter)) {
                $def = \Object_Objectbrick_Definition::getByKey($brickType);
                $def = $def->getFieldDefinition($brickKey);
                if(empty($label) && !empty($value)) {
                    if($def) {
                        $label = $def->getTitle();
                    }
                }


                if(is_object($value) && method_exists($value, $brickTypeGetter)) {
                    $value = $value->$brickTypeGetter();

                    if(is_object($value) && method_exists($value, $brickGetter)) {
                        $value = $value->$brickGetter();
                    } else {
                        $value = null;
                    }

                } else {
                    $value = null;
                }
            }

            $result = new \stdClass();
            $result->value = $value;
            $result->label = $label;

            if(empty($value) || (is_object($value) && method_exists($value, "isEmpty") && $value->isEmpty())) {
                $result->empty = true;
            } else {
                $result->empty = false;
            }

            $result->def = $def;
            return $result;

        }

        return null;
    }
}
