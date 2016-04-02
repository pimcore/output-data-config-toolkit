<?php
namespace Elements\OutputDataConfigToolkit;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;
use Elements\OutputDataConfigToolkit\ConfigElement\Operator as Operator;
use Elements\OutputDataConfigToolkit\ConfigElement\Value as Value;

class Service {

    /**
     * @static
     * @return \Elements\OutputDataConfigToolkit\ConfigElement\IConfigElement[]
     */
    public static function getOutputDataConfig($object, $channel, $class = null, $context = null) {

        if($class) {
            if(is_string($class)) {
                $objectClass = \Pimcore\Model\Object\ClassDefinition::getByName($class);
                if(empty($objectClass)) {
                    throw new \Exception("Class $class not found.");
                }
            } else if($class instanceof \Pimcore\Model\Object\ClassDefinition) {
                $objectClass = $class;
            } else {
                throw new \Exception("Invalid Parameter class - needs to be string or Object_Class");
            }
        } else {
            $objectClass = $object->getClass();
        }

        $outputDataConfig = OutputDefinition::getByO_IdClassIdChannel($object->getId(), $objectClass->getId(), $channel);
        if(empty($outputDataConfig)) {
            while(empty($outputDataConfig) && !empty($object)) {
                $object = $object->getParent();
                $outputDataConfig = OutputDefinition::getByO_IdClassIdChannel($object->getId(), $objectClass->getId(), $channel);
            }
        }

        return self::buildOutputDataConfig($outputDataConfig, $context);
    }

    /**
     * @param $outputDataConfig
     * @return \Elements\OutputDataConfigToolkit\ConfigElement\IConfigElement[]
     */
    public static function buildOutputDataConfig($outputDataConfig, $context = null) {
        $config = array();
        $jsonConfig = json_decode($outputDataConfig->getConfiguration());
        $config = self::doBuildConfig($jsonConfig, $config, $context);
        return $config;
    }


    private static function doBuildConfig($jsonConfig, $config, $context = null) {

        if(!empty($jsonConfig)) {
            foreach($jsonConfig as $configElement) {
                if($configElement->type == "value") {
                    $name = "Elements\\OutputDataConfigToolkit\\ConfigElement\\Value\\" . ucfirst($configElement->class);

                    if(class_exists($name)) {

                        if($name == 'Elements\OutputDataConfigToolkit\ConfigElement\Value\KeyValue') {

                            if($configElement->records) {
                                foreach($configElement->records as $index => $rec) {
                                    $config[] = new $name($configElement, $index, $context);
                                }
                            }
                        } else {
                            $config[] = new $name($configElement, $context);
                        }

                    }

                } else if($configElement->type == "operator") {
                    $name = "Elements\\OutputDataConfigToolkit\\ConfigElement\\Operator\\" . ucfirst($configElement->class);

                    if(!empty($configElement->childs)) {
                        $configElement->childs = self::doBuildConfig($configElement->childs, array(), $context);
                    }

                    if(class_exists($name)) {
                        $config[] = new $name($configElement, $context);
                    }

                }

            }
        }

        return $config;
    }


    /**
     * inits channels for root object
     */
    public static function initChannelsForRootobject() {
        $channels = Plugin::getChannels();

        $classList = new \Pimcore\Model\Object\ClassDefinition\Listing();
        $classList = $classList->load();

        foreach($classList as $class) {
            foreach($channels as $channel) {
                $def = OutputDefinition::getByO_IdClassIdChannel(1, $class->getId(), $channel);
                if(empty($def)) {
                    $def = new OutputDefinition();
                    $def->setO_Id(1);
                    $def->setO_ClassId($class->getId());
                    $def->setChannel($channel);
                    $def->save();
                }
            }
        }
    }

}
