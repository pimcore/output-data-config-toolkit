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


use Elements\OutputDataConfigToolkit as OutputDataConfigToolkit;

class OutputDataConfigToolkitBundle_AdminController extends \Pimcore\Controller\Action\Admin {

    public function init() {
        parent::init();
        $_REQUEST["systemLocale"] = $this->getLanguage();
    }

    public function getOutputConfigsAction() {
        OutputDataConfigToolkit\Service::initChannelsForRootobject();
        $channels = OutputDataConfigToolkit\Plugin::getChannels();

        $objectId = $this->_getParam("object_id");
        $object = \Pimcore\Model\Object\AbstractObject::getById($objectId);

        $classList = new \Pimcore\Model\Object\ClassDefinition\Listing();
        if($this->_getParam("class_id")) {
            $classList->setCondition("id = ?", $this->_getParam("class_id"));
        }

        $classList = $classList->load();

        $outputDefinitions = array();
        foreach($classList as $class) {
            foreach($channels as $channel) {
                $def = $this->getOutputDefinitionForObjectAndChannel($object, $class->getId(), $channel);
                $outputDefinitions[] = array(
                    "id" => $def->getId(),
                    "classname" => $this->view->translateAdmin($class->getName()),
                    "channel" => $this->view->translateAdmin($channel),
                    "object_id" => $def->getO_Id(),
                    "is_inherited" => $def->getO_Id() != $objectId
                );
            }
        }
        $this->_helper->json(array("success" => true, "data" => $outputDefinitions));
    }


    private function getOutputDefinitionForObjectAndChannel($object, $classId, $channel) {
        $outputDefinition = OutputDataConfigToolkit\OutputDefinition::getByO_IdClassIdChannel($object->getId(), $classId, $channel);
        if(empty($outputDefinition)) {
            $parent = $object->getParent();
            if(!empty($parent)) {
                return $this->getOutputDefinitionForObjectAndChannel($parent, $classId, $channel);
            }
        }
        return $outputDefinition;
    }

    public function resetOutputConfigAction() {
        try {
            $config = OutputDataConfigToolkit\OutputDefinition::getByID($this->_getParam("config_id"));
            $config->delete();
            $this->_helper->json(array("success" => true));
        } catch(Exception $e) {
            Logger::err($e->getMessage(), $e);
            $this->_helper->json(array("success" => false, "message" => $e->getMessage()));
        }
    }

    public function getOutputConfigAction() {
        try {
            $config = OutputDataConfigToolkit\OutputDefinition::getByID($this->_getParam("config_id"));

            $objectClass = \Pimcore\Model\Object\ClassDefinition::getById($config->getO_ClassId());
            $configuration = json_decode($config->getConfiguration());
            $configuration = $this->doGetAttributeLabels($configuration, $objectClass);

            $config->setConfiguration($configuration);
            $this->_helper->json(array("success" => true, "outputConfig" => $config));
        } catch(Exception $e) {
            Logger::err($e);
            $this->_helper->json(array("success" => false, "message" => $e->getMessage()));
        }
    }

    public function getOrCreateOutputConfigAction() {
        try {
            $config = OutputDataConfigToolkit\OutputDefinition::getByID($this->_getParam("config_id"));
            if(!$config) {

                if(is_numeric($this->getParam("class_id"))) {
                    $class = \Pimcore\Model\Object\ClassDefinition::getById($this->getParam("class_id"));
                } else {
                    $class = \Pimcore\Model\Object\ClassDefinition::getByName($this->getParam("class_id"));
                }
                if(!$class) {
                    throw new Exception("Class " . $this->getParam("class_id") . " not found.");
                }

                $config = OutputDataConfigToolkit\OutputDefinition::getByO_IdClassIdChannel($this->getParam("o_id"), $class->getId(), $this->getParam("channel"));
            }

            if($config) {
                $objectClass = \Pimcore\Model\Object\ClassDefinition::getById($config->getO_ClassId());
                $configuration = json_decode($config->getConfiguration());
                $configuration = $this->doGetAttributeLabels($configuration, $objectClass);
                $config->setConfiguration($configuration);
                $this->_helper->json(array("success" => true, "outputConfig" => $config));
            } else {
                $config = new OutputDataConfigToolkit\OutputDefinition();
                $config->setChannel($this->getParam("channel"));
                $config->setO_ClassId($class->getId());
                $config->setO_Id($this->getParam("o_id"));
                $config->save();

                $this->_helper->json(array("success" => true, "outputConfig" => $config));
            }

        } catch(Exception $e) {
            Logger::err($e->getMessage(), $e);
            $this->_helper->json(array("success" => false, "message" => $e->getMessage()));
        }
    }



    private function doGetAttributeLabels($configuration, $objectClass) {
        $newConfiguration = array();
        if(!empty($configuration)) {
            foreach($configuration as $c) {
                $newConfig = $c;
                if(!empty($newConfig->label)) {
                    $newConfig->text = $newConfig->label;
                } else {
                    $def = $this->getFieldDefinition($newConfig->attribute, $objectClass);
                    if($def) {
                        $newConfig->text = $this->view->translateAdmin($def->getTitle());
                    }

                    if($newConfig->dataType == "system") {
                        $newConfig->text = $newConfig->attribute;
                    }


                }
                $newConfig->childs = $this->doGetAttributeLabels($c->childs, $objectClass);
                $newConfiguration[] = $newConfig;

            }
        }
        return $newConfiguration;
    }

    public function getAttributeLabelsAction() {
        $configration = json_decode($this->getParam("configuration"));
        $class = \Pimcore\Model\Object\ClassDefinition::getById($this->getParam("classId"));

        $configration = $this->doGetAttributeLabels($configration, $class);
        $this->_helper->json(array("configuration" => $configration));
    }

    private function getFieldDefinition($attributeName, $objectClass) {
        $label = null;
        $attributeParts = explode("~", $attributeName);

        if (substr($attributeName, 0, 1) == "~") {
            // key value, ignore for now
        } else if(count($attributeParts) > 1) {
            $brickType = $attributeParts[0];
            $brickKey = $attributeParts[1];
        }

        $def = $objectClass->getFieldDefinition($attributeName);


        if(!empty($brickType)) {
            try {
                $def = \Pimcore\Model\Object\Objectbrick\Definition::getByKey($brickType);
                $def = $def->getFieldDefinition($brickKey);
            } catch(Exception $e) {
                Logger::err($e);
            }
        }

        if(empty($def) && $objectClass->getFieldDefinition("localizedfields")) {
            $def = $objectClass->getFieldDefinition("localizedfields")->getFieldDefinition($attributeName);
        }

        return $def;
    }


    public function getFieldDefinitionAction() {

        try {
            $objectClass = \Pimcore\Model\Object\ClassDefinition::getById($this->_getParam("class_id"));
            $def = $this->getFieldDefinition($this->_getParam("key"), $objectClass);
            $this->_helper->json(array("success" => true, "fieldDefinition" => $def));
        } catch(Exception $e) {
            Logger::err($e->getMessage(), $e);
            $this->_helper->json(array("success" => false, "message" => $e->getMessage()));
        }

    }

    public function saveOutputConfigAction() {
        try {
            $config = OutputDataConfigToolkit\OutputDefinition::getByID($this->_getParam("config_id"));

            $object = \Pimcore\Model\Object\AbstractObject::getById($this->_getParam("object_id"));
            if(empty($object)) {
                throw new Exception("Object with ID" . $this->_getParam("object_id") . " not found.");
            }
            if($config->getO_Id() == $this->_getParam("object_id")) {
                $config->setConfiguration($this->_getParam("config"));
                $config->save();
            } else {
                $newConfig = new OutputDataConfigToolkit\OutputDefinition();
                $newConfig->setChannel($config->getChannel());
                $newConfig->setO_ClassId($config->getO_ClassId());
                $newConfig->setO_Id($object->getId());
                $newConfig->setConfiguration($this->_getParam("config"));
                $newConfig->save();
            }
            $this->_helper->json(array("success" => true));
        } catch(Exception $e) {
            Logger::err($e->getMessage(), $e);
            $this->_helper->json(array("success" => false, "message" => $e->getMessage()));
        }
    }

}
