<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OutputDataConfigToolkitBundle\Controller;

use OutputDataConfigToolkitBundle\Event\InitializeEvent;
use OutputDataConfigToolkitBundle\Event\OutputDataConfigToolkitEvents;
use OutputDataConfigToolkitBundle\Event\SaveConfigEvent;
use OutputDataConfigToolkitBundle\OutputDefinition;
use OutputDataConfigToolkitBundle\Service;
use Pimcore\Controller\Traits\JsonHelperTrait;
use Pimcore\Controller\UserAwareController;
use Pimcore\Logger;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Objectbrick\Definition;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController
 *
 * @Route("/admin")
 */
class AdminController extends UserAwareController
{
    use JsonHelperTrait;

    public function __construct(protected TranslatorInterface $translator)
    {
    }

    /* @var string[] $defaultGridClasses */
    private $defaultGridClasses = [];

    /* @var bool $orderByName */
    private $orderByName = false;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/initialize")
     */
    public function initializeAction(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        $objectId = $request->get('id');
        $object = AbstractObject::getById($objectId);

        if (!$object) {
            $this->jsonResponse(['error' => true, 'object' => (object)[]]);
        }

        $event = new InitializeEvent($object);
        $eventDispatcher->dispatch($event, OutputDataConfigToolkitEvents::INITIALIZE);

        if ($event->getHideConfigTab()) {
            // do not show output config tab
            return $this->jsonResponse(['success' => true, 'object' => false]);
        }

        $data = ['id' => $event->getObject()->getId()];

        return $this->jsonResponse(['success' => true, 'object' => $data]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/get-output-configs")
     */
    public function getOutputConfigsAction(Request $request)
    {
        Service::initChannelsForRootobject();
        $channels = Service::getChannels();

        $objectId = $request->get('object_id');
        $object = AbstractObject::getById($objectId);

        $classList = $this->getFilteredClassDefinitionList($request);

        if ($this->getOrderByName()) {
            $classList->setOrderKey('name');
            $classList->setOrder('ASC');
        }

        $classList = $classList->load();

        $outputDefinitions = [];
        foreach ($classList as $class) {
            foreach ($channels as $channel) {
                $def = $this->getOutputDefinitionForObjectAndChannel($object, $class->getId(), $channel);
                if ($def === null) {
                    continue;
                }
                $outputDefinitions[] = [
                    'id' => $def->getId(),
                    'classname' => $this->translator->trans($class->getName(), [], 'admin'),
                    'channel' => $this->translator->trans($channel, [], 'admin'),
                    'object_id' => $def->getObjectId(),
                    'is_inherited' => $def->getObjectId() != $objectId
                ];
            }
        }

        return $this->jsonResponse(['success' => true, 'data' => $outputDefinitions]);
    }

    /**
     * @param $object
     * @param $classId
     * @param $channel
     *
     * @return OutputDefinition|null
     */
    private function getOutputDefinitionForObjectAndChannel($object, $classId, $channel)
    {
        $outputDefinition = OutputDefinition::getByObjectIdClassIdChannel($object->getId(), $classId, $channel);
        if (empty($outputDefinition)) {
            $parent = $object->getParent();
            if (!empty($parent)) {
                return $this->getOutputDefinitionForObjectAndChannel($parent, $classId, $channel);
            }
        }

        return $outputDefinition;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/reset-output-config")
     */
    public function resetOutputConfigAction(Request $request)
    {
        try {
            $config = OutputDefinition::getById($request->get('config_id'));
            $config->delete();

            return $this->jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            Logger::err($e->getMessage());

            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/get-output-config")
     */
    public function getOutputConfigAction(Request $request)
    {
        try {
            $config = OutputDefinition::getById($request->get('config_id'));

            $objectClass = ClassDefinition::getById($config->getClassId());
            $configuration = json_decode($config->getConfiguration());
            $configuration = $this->doGetAttributeLabels($configuration, $objectClass);

            $config->setConfiguration($configuration);

            return $this->jsonResponse(['success' => true, 'outputConfig' => $config]);
        } catch (\Exception $e) {
            Logger::err($e);

            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/get-or-create-output-config")
     */
    public function getOrCreateOutputConfigAction(Request $request)
    {
        try {
            $config = OutputDefinition::getById($request->get('config_id'));
            $class = null;
            if (!$config) {
                if (is_numeric($request->get('class_id'))) {
                    $class = ClassDefinition::getById($request->get('class_id'));
                } else {
                    $class = ClassDefinition::getByName($request->get('class_id'));
                }
                if (!$class) {
                    throw new \Exception('Class ' . $request->get('class_id') . ' not found.');
                }

                $config = OutputDefinition::getByObjectIdClassIdChannel($request->get('objectId'), $class->getId(), $request->get('channel'));
            }

            if ($config) {
                $objectClass = ClassDefinition::getById($config->getClassId());
                $configuration = json_decode($config->getConfiguration());
                $configuration = $this->doGetAttributeLabels($configuration, $objectClass);
                $config->setConfiguration($configuration);

                return $this->jsonResponse(['success' => true, 'outputConfig' => $config]);
            } else {
                $config = new OutputDefinition();
                $config->setChannel($request->get('channel'));
                $config->setClassId($class->getId());
                $config->setObjectId($request->get('objectId'));
                $config->save();

                return $this->jsonResponse(['success' => true, 'outputConfig' => $config]);
            }
        } catch (\Exception $e) {
            Logger::err($e->getMessage());

            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param \stdClass[]|null $configuration
     * @param ClassDefinition $objectClass
     *
     * @return array
     */
    private function doGetAttributeLabels($configuration, $objectClass, bool $sort = false)
    {
        $newConfiguration = [];
        if (!empty($configuration)) {
            foreach ($configuration as $c) {
                $newConfig = $c;
                if (!empty($newConfig->label)) {
                    $newConfig->text = $newConfig->label;
                } else {
                    $def = null;
                    if (isset($newConfig->attribute)) {
                        $def = $this->getFieldDefinition($newConfig->attribute, $objectClass);
                    }

                    if ($def) {
                        $newConfig->text = $this->translator->trans($def->getTitle(), [], 'admin');
                    }

                    if (isset($newConfig->dataType) && $newConfig->dataType == 'system' && isset($newConfig->attribute)) {
                        $newConfig->text = $newConfig->attribute;
                    }
                }

                $children = $this->doGetAttributeLabels($c->childs, $objectClass, $sort);
                if ($sort) {
                    $this->sortAttributes($children);
                }

                $newConfig->childs = $children;
                $newConfiguration[] = $newConfig;
            }
        }

        if ($sort) {
            $this->sortAttributes($newConfiguration);
        }

        return $newConfiguration;
    }

    private function sortAttributes(array &$attributes)
    {
        //@todo only sort if enabled in config...
        usort($attributes, function ($a1, $a2) {
            return strcmp($a1->text, $a2->text);
        });
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/get-attribute-labels")
     */
    public function getAttributeLabelsAction(Request $request)
    {
        $configration = json_decode($request->get('configuration'));
        $class = ClassDefinition::getById($request->get('classId'));

        $configration = $this->doGetAttributeLabels($configration, $class);

        return $this->jsonResponse(['configuration' => $configration]);
    }

    /**
     * @param string $attributeName
     * @param ClassDefinition $objectClass
     *
     * @return ClassDefinition\Data|null
     */
    private function getFieldDefinition($attributeName, $objectClass)
    {
        $brickType = null;
        $brickKey = null;
        $attributeParts = explode('~', $attributeName);

        if (substr($attributeName, 0, 1) == '~') {
            // key value, ignore for now
        } elseif (count($attributeParts) > 1) {
            $brickType = $attributeParts[0];
            $brickKey = $attributeParts[1];
        }

        $def = null;
        $classificationPrefix = '#cs#';

        if (substr($attributeName, 0, strlen($classificationPrefix)) == $classificationPrefix) {
            $attributeName = substr($attributeName, strlen($classificationPrefix));
            $classificationKeyParts = explode('#', $attributeName);
            $classificationKeyId = $classificationKeyParts[0];

            if (!empty($classificationKeyId)) { // for localized classification store attributes, such as #cs##Ba the key will be empty.
                if ($keyConfig = KeyConfig::getById((int) $classificationKeyId)) {
                    $def = \Pimcore\Model\DataObject\Classificationstore\Service::getFieldDefinitionFromKeyConfig($keyConfig);
                }
            }
        } elseif ($brickKey && strpos($brickType, '?') === 0) {
            $definitionJson = substr($brickType, 1);
            $brickInfos = json_decode($definitionJson);
            $containerKey = $brickInfos->containerKey;
            $brickfield = $brickInfos->brickfield;
            try {
                $brickDef = Definition::getByKey($containerKey);
                $def = $brickDef->getFieldDefinition($brickKey);
                if (empty($def)) {
                    $lf = $brickDef->getFieldDefinition('localizedfields');
                    if ($lf instanceof ClassDefinition\Data\Localizedfields) {
                        $def = $lf->getFieldDefinition($brickfield);
                    }
                }
            } catch (\Exception $e) {
                Logger::err($e);
            }
        } else {
            $def = $objectClass->getFieldDefinition($attributeName);
        }

        if (!$def && !empty($brickType)) {
            try {
                $def = Definition::getByKey($brickType);
                $def = $def->getFieldDefinition($brickKey);
            } catch (\Exception $e) {
                Logger::err($e);
            }
        }

        if (empty($def)) {
            $lf = $objectClass->getFieldDefinition('localizedfields');
            if ($lf instanceof ClassDefinition\Data\Localizedfields) {
                $def = $lf->getFieldDefinition($attributeName);
            }
        }

        return $def;
    }

    /**
     * @param Request $request
     * @Route("/get-field-definition")
     *
     * @return JsonResponse
     */
    public function getFieldDefinitionAction(Request $request)
    {
        try {
            $objectClass = ClassDefinition::getById($request->get('class_id'));
            $def = $this->getFieldDefinition($request->get('key'), $objectClass);

            return $this->jsonResponse(['success' => true, 'fieldDefinition' => $def]);
        } catch (\Exception $e) {
            Logger::err($e->getMessage());

            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @Route("/save-output-config")
     *
     * @return JsonResponse
     */
    public function saveOutputConfigAction(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        try {
            $config = OutputDefinition::getById($request->get('config_id'));

            $object = AbstractObject::getById($request->get('object_id'));
            if (empty($object)) {
                throw new \Exception('Data Object with ID' . $request->get('object_id') . ' not found.');
            }
            if ($config->getObjectId() != $request->get('object_id')) {
                $newConfig = new OutputDefinition();
                $newConfig->setChannel($config->getChannel());
                $newConfig->setClassId($config->getClassId());
                $newConfig->setObjectId($object->getId());
                $config = $newConfig;
            }

            $configJson = $request->get('config');
            $config->setConfiguration($configJson);

            $event = new SaveConfigEvent($config);
            $eventDispatcher->dispatch($event, OutputDataConfigToolkitEvents::SAVE_CONFIG_EVENT);

            if ($event->doSortAttributes()) {
                $objectClass = ClassDefinition::getById($config->getClassId());
                $configuration = json_decode($configJson);
                $configuration = $this->doGetAttributeLabels($configuration, $objectClass, true);
                $configJson = json_encode($configuration);
                $config->setConfiguration($configJson);
            }
            $config->save();

            return $this->jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            Logger::err($e->getMessage());

            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     *
     * @return ClassDefinition\Listing
     */
    private function getFilteredClassDefinitionList(Request $request): ClassDefinition\Listing
    {
        $classList = new ClassDefinition\Listing();

        if ($request->get('class_id')) {
            $classList->setCondition('id = ?', $request->get('class_id'));
        } elseif (!empty($this->defaultGridClasses)) {
            $allowedClassIds = [];
            foreach ($this->defaultGridClasses as $allowedClass) {
                $classNamespace = 'Pimcore\\Model\\DataObject\\';
                $allowedClassArr = explode('\\', $allowedClass);
                $allowedClassFull = $classNamespace . array_pop($allowedClassArr);
                if (class_exists($allowedClassFull)) {
                    $allowedClassIds[] = call_user_func([$allowedClassFull, 'classId']);
                } else {
                    $allowedClassIds[] = $allowedClass;
                }
            }
            $classList->addConditionParam("id IN ('" . implode("','", $allowedClassIds) . "')");
        }

        return $classList;
    }

    /**
     * @param string[] $defaultGridClasses
     *
     * @return $this
     */
    public function setDefaultGridClasses(array $defaultGridClasses): static
    {
        $this->defaultGridClasses = $defaultGridClasses;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDefaultGridClasses(): array
    {
        return $this->defaultGridClasses;
    }

    /**
     * @return bool
     */
    public function getOrderByName(): bool
    {
        return $this->orderByName;
    }

    /**
     * @param bool $orderByName
     *
     * @return $this
     */
    public function setOrderByName(bool $orderByName): static
    {
        $this->orderByName = $orderByName;

        return $this;
    }
}
