<?php
/**
 * Created by PhpStorm.
 * User: jraab
 * Date: 12.02.2019
 * Time: 16:49
 */

namespace OutputDataConfigToolkitBundle\Controller;


use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject;

/**
 * Class ClassController
 * @package OutputDataConfigToolkitBundle\Controller
 *
 */
class ClassController extends \Pimcore\Bundle\AdminBundle\Controller\AdminController
{

    /**
     * @Route("/get-class-definition-for-column-config", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
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
        $class->setFieldDefinitions(null);

        $result = [];

        $result['objectColumns']['childs'] = $layoutDefinitions->getChilds();
        $result['objectColumns']['nodeLabel'] = 'object_columns';
        $result['objectColumns']['nodeType'] = 'object';

        // array("id", "fullpath", "published", "creationDate", "modificationDate", "filename", "classname");
        $systemColumnNames = DataObject\Concrete::$systemColumnNames;
        $systemColumns = [];
        foreach ($systemColumnNames as $systemColumn) {
            $systemColumns[] = ['title' => $systemColumn, 'name' => $systemColumn, 'datatype' => 'data', 'fieldtype' => 'system'];
        }
        $result['systemColumns']['nodeLabel'] = 'system_columns';
        $result['systemColumns']['nodeType'] = 'system';
        $result['systemColumns']['childs'] = $systemColumns;

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
                        $result[$key]['childs'] = $brickDefinition->getLayoutdefinitions()->getChilds();
                        break;
                    }
                }
            }
        }

        $targetObjectId = $request->get('target_oid');

        if ($targetObject = DataObject\AbstractObject::getById($targetObjectId)) {
            $class->setFieldDefinitions($fieldDefinitions);
            DataObject\Service::enrichLayoutDefinition($result['objectColumns']['childs'][0], $targetObject);
        }


        return $this->adminJson($result);
    }

}