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

use Pimcore\Bundle\AdminBundle\Controller\Rest\AbstractRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RestController
 * @Route("/rest")
 */
class RestController extends AbstractRestController {

    private static $_OUTPUTDATA_CONFIG_TABLE_NAME = "bundle_outputdataconfigtoolkit_outputdefinition";


    /**
     * Read the output data config channel tabel and return everything for a specific classId
     * (e.g. product class Id), which must be passed via an argument.
     * Request must contain: apikey (REST api key to access the server), and classId parameter.
     * @return JSON object with status information and the forwarded db results.
     *
     * @Route("/configdata")
     */
    public function configdataAction(Request $request) {
        $filterId = $request->get('remoteFilterId');
        $responseObj = new \stdClass();
        $responseObj->status = "NOTOK";
        $responseObj->msg    = "No action performed.";
        $responseObj->result = [];

        $classId = $request->get('classId');
        if (!$classId) {
            $responseObj->msg = 'Parameter "classId" not provided.';
        } else {
            $sql = sprintf("SELECT * FROM %s WHERE o_classId=?", self::$_OUTPUTDATA_CONFIG_TABLE_NAME);
            $db = \Pimcore\Db::get();
            $records = $db->fetchAll($sql, [$classId]);
            $responseObj->result = $records;
            $responseObj->status = "OK";
            $responseObj->msg = sprintf("Found %d records.", count($records));
        }
        return $this->adminJson($responseObj);
    }

}
