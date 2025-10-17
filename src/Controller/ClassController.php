<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace OutputDataConfigToolkitBundle\Controller;

use Pimcore\Helper\ParameterBagHelper;
use Doctrine\DBAL\Exception\TableNotFoundException;
use OutputDataConfigToolkitBundle\Constant\ColumnConfigDisplayMode;
use OutputDataConfigToolkitBundle\Event;
use Pimcore\Controller\Traits\JsonHelperTrait;
use Pimcore\Controller\UserAwareController;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\FactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClassController
 *
 * @package OutputDataConfigToolkitBundle\Controller
 *
 */
class ClassController extends UserAwareController
{
    use JsonHelperTrait;

    // @var string $classificationDisplayMode
    protected $classificationDisplayMode;

    // @var bool $classificationGroupedDisplay
    protected bool $classificationGroupedDisplay;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    #[Route('/get-class-definition-for-column-config', methods: ['GET'])]
    public function getClassDefinitionForColumnConfigAction(Request $request, EventDispatcherInterface $eventDispatcher, FactoryInterface $factory)
    {
        $classId = $request->query->getString('id');
        $class = DataObject\ClassDefinition::getById($classId);
        $objectId = ParameterBagHelper::getInt($request->query, 'oid');

        $filteredDefinitions = DataObject\Service::getCustomLayoutDefinitionForGridColumnConfig($class, $objectId);

        $layoutDefinitions = isset($filteredDefinitions['layoutDefinition']) ? $filteredDefinitions['layoutDefinition'] : false;
        $filteredFieldDefinition = isset($filteredDefinitions['fieldDefinition']) ? $filteredDefinitions['fieldDefinition'] : false;

        $fieldDefinitions = $class->getFieldDefinitions();
        $class->setFieldDefinitions([]);

        $result = [];

        $result['objectColumns']['children'] = $layoutDefinitions->getChildren();
        $result['objectColumns']['nodeLabel'] = 'object_columns';
        $result['objectColumns']['nodeType'] = 'object';

        //DataObject\Concrete::SYSTEM_COLUMN_NAMES
        $systemColumnNames = ['id', 'fullpath', 'key', 'published', 'creationDate', 'modificationDate', 'filename', 'classname'];
        $systemColumns = [];
        foreach ($systemColumnNames as $systemColumn) {
            $systemColumns[] = ['title' => $systemColumn, 'name' => $systemColumn, 'datatype' => 'data', 'fieldtype' => 'system'];
        }
        $result['systemColumns']['nodeLabel'] = 'system_columns';
        $result['systemColumns']['nodeType'] = 'system';
        $result['systemColumns']['children'] = $systemColumns;

        $list = new DataObject\Objectbrick\Definition\Listing();
        $list = $list->load();

        foreach ($list as $brickDefinition) {
            $classDefs = $brickDefinition->getClassDefinitions();
            if (!empty($classDefs)) {
                foreach ($classDefs as $classDef) {
                    if ($classDef['classname'] == $class->getName()) {
                        $fieldName = $classDef['fieldname'];
                        if ($filteredFieldDefinition && !$filteredFieldDefinition[$fieldName]) {
                            continue;
                        }

                        $key = $brickDefinition->getKey();

                        $result[$key]['nodeLabel'] = $key;
                        $result[$key]['brickField'] = $fieldName;
                        $result[$key]['nodeType'] = 'objectbricks';
                        $result[$key]['children'] = $brickDefinition->getLayoutdefinitions()->getChildren();

                        break;
                    }
                }
            }
        }

        $this->considerClassificationStoreForColumnConfig($request, $class, $fieldDefinitions, $result, $eventDispatcher, $factory);

        return $this->jsonResponse($result);
    }

    /**
     * @param Request $request
     * @param DataObject\ClassDefinition|null $class
     * @param array $fieldDefinitions
     * @param array $result
     */
    private function considerClassificationStoreForColumnConfig(Request $request, ?DataObject\ClassDefinition $class, array $fieldDefinitions, array &$result, EventDispatcherInterface $eventDispatcher, FactoryInterface $factory): void
    {
        $displayMode = $this->getClassificationDisplayMode();

        if ($displayMode == ColumnConfigDisplayMode::NONE) {
            return;
        }

        $enrichment = false;
        $grouped = $this->getClassificationGroupedDisplay();
        if ($displayMode == ColumnConfigDisplayMode::DATA_OBJECT || $displayMode == ColumnConfigDisplayMode::RELEVANT) {
            $targetObjectId = ParameterBagHelper::getInt($request->query, 'target_oid');

            if ($targetObject = DataObject\Concrete::getById($targetObjectId)) {
                $class->setFieldDefinitions($fieldDefinitions);

                try {
                    // @todo: is there a better way to check if a classification group is assigned to the class?
                    $idField = 'id';
                    $enrichment = Db::get()->fetchOne("SELECT EXISTS (SELECT * FROM object_classificationstore_groups_{$class->getId()} WHERE `{$idField}` = '{$targetObjectId}')");
                    if ($enrichment) {
                        DataObject\Service::enrichLayoutDefinition($result['objectColumns']['children'][0], $targetObject);
                    }
                } catch (TableNotFoundException $exception) {
                    $enrichment = false;
                }
            }
        }

        if ($displayMode == ColumnConfigDisplayMode::ALL && $grouped === true) {
            $class->setFieldDefinitions($fieldDefinitions);
            $classString = 'Pimcore\\Model\\DataObject\\' . $class->getName();
            $targetObjectId = intval($request->get('target_oid'));
            $targetObject = DataObject\Concrete::getById($targetObjectId);
            $tmpObject = $factory->build($classString);
            /** @var DataObject\Concrete $tmpObject */
            $db = Db::get();
            foreach ($class->getFieldDefinitions() as $fieldDefinition) {
                if (!$fieldDefinition instanceof DataObject\ClassDefinition\Data\Classificationstore) {
                    continue;
                }

                $storeId = $fieldDefinition->getStoreId();
                $store = new DataObject\Classificationstore();

                $groupIds = [];
                $sql = 'SELECT `id` FROM `classificationstore_groups`';
                if ($storeId > 0) {
                    $sql = 'SELECT `id` FROM `classificationstore_groups`  WHERE `storeId` = ' . intval($storeId);
                }

                $queryResult = $db->executeQuery($sql);

                while ($row = $queryResult->fetchAssociative()) {
                    $groupIds[intval($row['id'])] = true;
                }

                $event = new Event\GroupClassificationStoreEvent($targetObject, $tmpObject, $fieldDefinition, $groupIds, $storeId);
                $eventDispatcher->dispatch($event, Event\OutputDataConfigToolkitEvents::GROUP_CLASSIFICATION_STORE_EVENT);

                $store->setActiveGroups($event->getActiveGroups());
                $store->setClass($class);
                $store->setFieldname($fieldDefinition->getName());
                $store->setObject($tmpObject);
                $tmpObject->set($fieldDefinition->getName(), $store);
            }

            DataObject\Service::enrichLayoutDefinition($result['objectColumns']['children'][0], $tmpObject);
        }

        if ($grouped === false && $displayMode == ColumnConfigDisplayMode::ALL || ($displayMode == ColumnConfigDisplayMode::RELEVANT && !$enrichment)) {
            $keyConfigDefinitions = [];
            $keyConfigs = new Classificationstore\KeyConfig\Listing();
            $keyConfigs = $keyConfigs->load();

            foreach ($keyConfigs as $keyConfig) {
                $definition = Classificationstore\Service::getFieldDefinitionFromKeyConfig($keyConfig);
                $definition->setTooltip($definition->getName() . ' - ' . $keyConfig->getDescription());
                $keyConfigDefinitions[] = [
                    'definition' => $definition,
                    'id' => $keyConfig->getId(),
                    'name' => $keyConfig->getName()
                ];
            }

            $result['classificationColumns'] = [
                'nodeType' => 'classificationstore',
                'nodeLabel' => 'classificationstore',
                'children' => $keyConfigDefinitions,
            ];
        }
    }

    /**
     * @param string $classificationDisplayMode
     */
    public function setClassificationDisplayMode(string $classificationDisplayMode)
    {
        $this->classificationDisplayMode = $classificationDisplayMode;
    }

    /**
     * @return string
     */
    public function getClassificationDisplayMode(): string
    {
        return $this->classificationDisplayMode;
    }

    /**
     * @param bool $grouped
     */
    public function setClassificationGroupedDisplay(bool $grouped)
    {
        $this->classificationGroupedDisplay = $grouped;
    }

    /**
     *
     * @return bool
     */
    public function getClassificationGroupedDisplay(): bool
    {
        return $this->classificationGroupedDisplay;
    }
}
