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


namespace OutputDataConfigToolkitBundle\Controller;

use OutputDataConfigToolkitBundle\OutputDefinition;
use OutputDataConfigToolkitBundle\Service;
use Pimcore\Logger;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @Route("/admin")
 */
class AdminController extends \Pimcore\Bundle\AdminBundle\Controller\AdminController {

    /**
     * @param Request $request
     * @Route("/get-output-configs")
     */
    public function getOutputConfigsAction(Request $request) {
        Service::initChannelsForRootobject();
        $channels = Service::getChannels();

        $objectId = $request->get("object_id");
        $object = AbstractObject::getById($objectId);

        $classList = new \Pimcore\Model\DataObject\ClassDefinition\Listing();
        if($request->get("class_id")) {
            $classList->setCondition("id = ?", $request->get("class_id"));
        }

        $classList = $classList->load();

        $translator = $this->get("translator");

        $outputDefinitions = array();
        foreach($classList as $class) {
            foreach($channels as $channel) {
                $def = $this->getOutputDefinitionForObjectAndChannel($object, $class->getId(), $channel);
                $outputDefinitions[] = array(
                    "id" => $def->getId(),
                    "classname" => $translator->trans($class->getName(), [], 'admin'),
                    "channel" => $translator->trans($channel, [], 'admin'),
                    "object_id" => $def->getO_Id(),
                    "is_inherited" => $def->getO_Id() != $objectId
                );
            }
        }
        return $this->adminJson(array("success" => true, "data" => $outputDefinitions));
    }


    private function getOutputDefinitionForObjectAndChannel($object, $classId, $channel) {
        $outputDefinition = OutputDefinition::getByO_IdClassIdChannel($object->getId(), $classId, $channel);
        if(empty($outputDefinition)) {
            $parent = $object->getParent();
            if(!empty($parent)) {
                return $this->getOutputDefinitionForObjectAndChannel($parent, $classId, $channel);
            }
        }
        return $outputDefinition;
    }

    /**
     * @param Request $request
     * @Route("/reset-output-config")
     */
    public function resetOutputConfigAction(Request $request) {
        try {
            $config = OutputDefinition::getByID($request->get("config_id"));
            $config->delete();
            return $this->adminJson(array("success" => true));
        } catch(\Exception $e) {
            Logger::err($e->getMessage(), $e);
            return $this->adminJson(array("success" => false, "message" => $e->getMessage()));
        }
    }

    /**
     * @param Request $request
     * @Route("/get-output-config")
     */
    public function getOutputConfigAction(Request $request) {
        try {
            $config = OutputDefinition::getByID($request->get("config_id"));

            $objectClass = ClassDefinition::getById($config->getO_ClassId());
            $configuration = json_decode($config->getConfiguration());
            $configuration = $this->doGetAttributeLabels($configuration, $objectClass);

            $config->setConfiguration($configuration);
            return $this->adminJson(array("success" => true, "outputConfig" => $config));
        } catch(\Exception $e) {
            Logger::err($e);
            return $this->adminJson(array("success" => false, "message" => $e->getMessage()));
        }
    }

    /**
     * @param Request $request
     * @Route("/get-or-create-output-config")
     */
    public function getOrCreateOutputConfigAction(Request $request) {
        try {
            $config = OutputDefinition::getByID($request->get("config_id"));
            if(!$config) {

                if(is_numeric($request->get("class_id"))) {
                    $class = ClassDefinition::getById($request->get("class_id"));
                } else {
                    $class = ClassDefinition::getByName($request->get("class_id"));
                }
                if(!$class) {
                    throw new \Exception("Class " . $request->get("class_id") . " not found.");
                }

                $config = OutputDefinition::getByO_IdClassIdChannel($request->get("o_id"), $class->getId(), $request->get("channel"));
            }

            if($config) {
                $objectClass = ClassDefinition::getById($config->getO_ClassId());
                $configuration = json_decode($config->getConfiguration());
                $configuration = $this->doGetAttributeLabels($configuration, $objectClass);
                $config->setConfiguration($configuration);
                return $this->adminJson(array("success" => true, "outputConfig" => $config));
            } else {
                $config = new OutputDefinition();
                $config->setChannel($request->get("channel"));
                $config->setO_ClassId($class->getId());
                $config->setO_Id($request->get("o_id"));
                $config->save();

                return $this->adminJson(array("success" => true, "outputConfig" => $config));
            }

        } catch(\Exception $e) {
            Logger::err($e->getMessage(), $e);
            return $this->adminJson(array("success" => false, "message" => $e->getMessage()));
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
                        $translator = $this->get("translator");
                        $newConfig->text = $translator->trans($def->getTitle(), [], "admin");
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

    /**
     * @param Request $request
     * @Route("/get-attribute-labels")
     */
    public function getAttributeLabelsAction(Request $request) {
        $configration = json_decode($request->get("configuration"));
        $class = ClassDefinition::getById($request->get("classId"));

        $configration = $this->doGetAttributeLabels($configration, $class);
        return $this->adminJson(array("configuration" => $configration));
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

        $def = null;
        $brickInfos = null;
        if ($brickKey && strpos($brickType, '?') === 0) {
            $definitionJson = substr($brickType, 1);
            $brickInfos = json_decode($definitionJson);
            $containerKey = $brickInfos->containerKey;
            $fieldName = $brickInfos->fieldname;
            $brickfield = $brickInfos->brickfield;
            try {
                $brickDef = \Pimcore\Model\DataObject\Objectbrick\Definition::getByKey($containerKey);
                $def = $brickDef->getFieldDefinition($brickKey);
                if(empty($def) && $brickDef->getFieldDefinition("localizedfields")) {
                    $def = $brickDef->getFieldDefinition("localizedfields")->getFieldDefinition($brickfield);
                }
            } catch(\Exception $e) {
                Logger::err($e);
            }
        } else {
            $def = $objectClass->getFieldDefinition($attributeName);
        }

        if(!$def && !empty($brickType)) {
            try {
                $def = \Pimcore\Model\DataObject\Objectbrick\Definition::getByKey($brickType);
                $def = $def->getFieldDefinition($brickKey);
            } catch(\Exception $e) {
                Logger::err($e);
            }
        }

        if(empty($def) && $objectClass->getFieldDefinition("localizedfields")) {
            $def = $objectClass->getFieldDefinition("localizedfields")->getFieldDefinition($attributeName);
        }

        return $def;
    }


    /**
     * @param Request $request
     * @Route("/get-field-definition")
     */
    public function getFieldDefinitionAction(Request $request) {

        try {
            $objectClass = \Pimcore\Model\Object\ClassDefinition::getById($request->get("class_id"));
            $def = $this->getFieldDefinition($request->get("key"), $objectClass);
            return $this->adminJson(array("success" => true, "fieldDefinition" => $def));
        } catch(\Exception $e) {
            Logger::err($e->getMessage(), $e);
            return $this->adminJson(array("success" => false, "message" => $e->getMessage()));
        }

    }

    /**
     * @param Request $request
     * @Route("/save-output-config")
     */
    public function saveOutputConfigAction(Request $request) {
        try {
            $config = OutputDefinition::getByID($request->get("config_id"));

            $object = AbstractObject::getById($request->get("object_id"));
            if(empty($object)) {
                throw new \Exception("Data Object with ID" . $request->get("object_id") . " not found.");
            }
            if($config->getO_Id() == $request->get("object_id")) {
                $config->setConfiguration($request->get("config"));
                $config->save();
            } else {
                $newConfig = new OutputDefinition();
                $newConfig->setChannel($config->getChannel());
                $newConfig->setO_ClassId($config->getO_ClassId());
                $newConfig->setO_Id($object->getId());
                $newConfig->setConfiguration($request->get("config"));
                $newConfig->save();
            }
            return $this->adminJson(array("success" => true));
        } catch(\Exception $e) {
            Logger::err($e->getMessage(), $e);
            return $this->adminJson(array("success" => false, "message" => $e->getMessage()));
        }
    }

}
