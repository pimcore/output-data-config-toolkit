<?php

use Elements\OutputDataConfigToolkit as OutputDataConfigToolkit;

class Elements_OutputDataConfigToolkit_RestController extends \Pimcore\Controller\Action\Webservice {

    private static $_OUTPUTDATA_CONFIG_TABLE_NAME = "plugin_outputdataconfigtoolkit_outputdefinition";

    public function init() {
        parent::init();

    }
    /**
     * Read the output data config channel tabel and return everything for a specific classId
     * (e.g. product class Id), which must be passed via an argument.
     * Request must contain: apikey (REST api key to access the server), and classId parameter.
     * @return JSON object with status information and the forwarded db results.
     */
    public function configdataAction() {
        $this->disableViewAutoRender();
        $this->disableLayout();
        $this->disableViewAutoRender();
        $filterId = $this->getParam('remoteFilterId');
        $responseObj = new \stdClass();
        $responseObj->status = "NOTOK";
        $responseObj->msg    = "No action performed.";
        $responseObj->result = [];

        $classId = $this->getParam('classId');
        if (!$classId) {
            $responseObj->msg = 'Parameter "classId" not provided.';
        } else {
            $sql = sprintf("SELECT * FROM %s WHERE o_classId=?", self::$_OUTPUTDATA_CONFIG_TABLE_NAME);
            $db = Pimcore\Db::get();
            $records = $db->fetchAll($sql, [$classId]);
            $responseObj->result = $records;
            $responseObj->status = "OK";
            $responseObj->msg = sprintf("Found %d records.", count($records));
        }
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $data = json_encode($responseObj);
        echo $data;
    }

}
