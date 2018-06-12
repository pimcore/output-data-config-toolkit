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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace OutputDataConfigToolkitBundle;

use OutputDataConfigToolkitBundle\ConfigElement\IConfigElement;
use Pimcore\Model\DataObject\ClassDefinition;

class Service {

    /**
     * @static
     * @return IConfigElement[]
     */
    public static function getOutputDataConfig($object, $channel, $class = null, $context = null) {

        if($class) {
            if(is_string($class)) {
                $objectClass = ClassDefinition::getByName($class);
                if(empty($objectClass)) {
                    throw new \Exception("Class $class not found.");
                }
            } else if($class instanceof ClassDefinition) {
                $objectClass = $class;
            } else {
                throw new \Exception("Invalid Parameter class - needs to be string or ClassDefinition");
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
     * @return IConfigElement[]
     */
    public static function buildOutputDataConfig($outputDataConfig, $context = null) {
        $config = array();
        $jsonConfig = json_decode($outputDataConfig->getConfiguration());
        $config = self::doBuildConfig($jsonConfig, $config, $context);
        return $config;
    }

    private static function locateOperatorConfigClass($configElement) : string {
        $namespaces = [
            "\\OutputDataConfigToolkitBundle\\ConfigElement\\Operator\\",
            "\\AppBundle\\OutputDataConfigToolkit\\ConfigElement\\Operator\\"
        ];

        foreach ($namespaces as $namespace) {
            $name = $namespace.ucfirst($configElement->class);
            if(class_exists($name)) {
                return $name;
            }
        }
        return "";
    }


    private static function doBuildConfig($jsonConfig, $config, $context = null) {

        if(!empty($jsonConfig)) {
            foreach($jsonConfig as $configElement) {
                if($configElement->type == "value") {
                    $name = "\\OutputDataConfigToolkitBundle\\ConfigElement\\Value\\" . ucfirst($configElement->class);

                    if(class_exists($name)) {
                        $config[] = new $name($configElement, $context);
                    }

                } else if($configElement->type == "operator") {

                    $className = self::locateOperatorConfigClass($configElement);
                    if(!empty($configElement->childs)) {
                        $configElement->childs = self::doBuildConfig($configElement->childs, array(), $context);
                    }

                    if(!empty($className)) {
                        $config[] = new $className($configElement, $context);
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
        $channels = self::getChannels();

        $classList = new ClassDefinition\Listing();
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


    public static function getChannels() {
        $config = self::getConfig();

        $channels = [];
        $channelsConfig = $config['channels'];
        if($channelsConfig) {
            foreach($channelsConfig as $c) {
                $channels[] = (string)$c;
            }
        }
        return $channels;
    }

    public static function getConfig() {
        $file = \Pimcore\Config::locateConfigFile("outputdataconfig/config.php");
        if(file_exists($file)) {
            $config = include($file);
        } else {
            \Pimcore\File::put($file, to_php_data_file_format([]));
            $config = [];
        }
        return $config;
    }


}
