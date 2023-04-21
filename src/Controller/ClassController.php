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

use Doctrine\DBAL\Exception\TableNotFoundException;
use OutputDataConfigToolkitBundle\Constant\ColumnConfigDisplayMode;
use Pimcore\Controller\Traits\JsonHelperTrait;
use Pimcore\Controller\UserAwareController;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Classificationstore;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClassController
 *
 * @package OutputDataConfigToolkitBundle\Controller
 *
 */
class ClassController extends UserAwareController
{
    use JsonHelperTrait;

    /* @var string $classificationDisplayMode */
    protected $classificationDisplayMode;

    /**
     * @Route("/get-class-definition-for-column-config", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getClassDefinitionForColumnConfigAction(Request $request)
    {
        $classId = $request->get('id');
        $class = DataObject\ClassDefinition::getById($classId);
        $objectId = intval($request->get('oid'));

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

        $this->considerClassificationStoreForColumnConfig($request, $class, $fieldDefinitions, $result);

        return $this->jsonResponse($result);
    }

    /**
     * @param Request $request
     * @param DataObject\ClassDefinition|null $class
     * @param array $fieldDefinitions
     * @param array $result
     */
    private function considerClassificationStoreForColumnConfig(Request $request, ?DataObject\ClassDefinition $class, array $fieldDefinitions, array &$result): void
    {
        $displayMode = $this->getClassificationDisplayMode();

        if ($displayMode == ColumnConfigDisplayMode::NONE) {
            return;
        }

        $enrichment = false;
        if ($displayMode == ColumnConfigDisplayMode::DATA_OBJECT || $displayMode == ColumnConfigDisplayMode::RELEVANT) {
            $targetObjectId = $request->get('target_oid');

            if ($targetObject = DataObject\Concrete::getById($targetObjectId)) {
                $class->setFieldDefinitions($fieldDefinitions);

                try {
                    // @todo: is there a better way to check if a classification group is assigned to the class?
                    $idField = DataObject\Service::getVersionDependentDatabaseColumnName('id');
                    $enrichment = Db::get()->fetchOne("SELECT EXISTS (SELECT * FROM object_classificationstore_groups_{$class->getId()} WHERE `{$idField}` = '{$targetObjectId}')");
                    if ($enrichment) {
                        DataObject\Service::enrichLayoutDefinition($result['objectColumns']['children'][0], $targetObject);
                    }
                } catch (TableNotFoundException $exception) {
                    $enrichment = false;
                }
            }
        }

        if ($displayMode == ColumnConfigDisplayMode::ALL || ($displayMode == ColumnConfigDisplayMode::RELEVANT && !$enrichment)) {
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
}
