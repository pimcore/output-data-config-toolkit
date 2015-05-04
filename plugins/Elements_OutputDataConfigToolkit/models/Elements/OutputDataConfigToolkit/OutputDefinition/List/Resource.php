<?php

class Elements_OutputDataConfigToolkit_OutputDefinition_List_Resource extends Pimcore_Model_List_Resource_Abstract {

    /**
     * @return array
     */
    public function load() {
        $configs = array();

        $unitIds = $this->db->fetchAll("SELECT o_id, o_configId, channel FROM " . Elements_OutputDataConfigToolkit_OutputDefinition_Resource::TABLE_NAME .
                                                 $this->getCondition() . $this->getOrder() . $this->getOffsetLimit());

        foreach ($unitIds as $row) {
            $configs[] = Elements_OutputDataConfigToolkit_OutputDefinition::getByO_IdClassIdChannel($row['o_id'], $row['o_classId'], $row['channel']);
        }

        $this->model->setOutputDefinitions($configs);

        return $configs;
    }

    public function getTotalCount() {
        $amount = $this->db->fetchRow("SELECT COUNT(*) as amount FROM `" . Elements_OutputDataConfigToolkit_OutputDefinition_Resource::TABLE_NAME . "`" . $this->getCondition());
        return $amount["amount"];
    }

}