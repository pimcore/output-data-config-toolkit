<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace Elements\OutputDataConfigToolkit\ConfigElement\Value;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;
use Pimcore\Model\Object\AbstractObject;

class DefaultValue extends ConfigElement\AbstractConfigElement {

    public function getLabeledValue($object) {

        $attributeParts = explode("~", $this->attribute);
        $label = $this->label;

        $getter = "get" . ucfirst($this->attribute);

        if (substr($this->attribute, 0, 1) == "~") {
            // key value, ignore for now
        } else if(count($attributeParts) > 1) {
            $brickType = $attributeParts[0];
            $brickKey = $attributeParts[1];

            $getter = "get" . ucfirst(\Pimcore\Model\Object\Service::getFieldForBrickType($object->getClass(), $brickType));
            $brickTypeGetter = "get" . ucfirst($brickType);
            $brickGetter = "get" . ucfirst($brickKey);
        }
        if(method_exists($object, $getter) || $object instanceof \OnlineShop_Framework_ProductList_DefaultMockup) {
            $value = $object->$getter();

            if($object instanceof \OnlineShop_Framework_ProductList_DefaultMockup || $object instanceof AbstractObject) {
                $def = $object->getClass()->getFieldDefinition($this->attribute);
                if(empty($label)) {
                    if($def) {
                        $label =  $def->getTitle();
                    }
                }

                if(!empty($value) && !empty($brickGetter)) {
                    $def = \Pimcore\Model\Object\Objectbrick\Definition::getByKey($brickType);
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
