<?php
namespace Elements\OutputDataConfigToolkit\OutputDefinition\Listing;

class Dao extends \Pimcore\Model\Listing\Dao\AbstractDao {

    /**
     * @return array
     */
    public function load() {
        $configs = array();

        $unitIds = $this->db->fetchAll("SELECT o_id, o_configId, channel FROM " . \Elements\OutputDataConfigToolkit\OutputDefinition\Dao::TABLE_NAME .
                                                 $this->getCondition() . $this->getOrder() . $this->getOffsetLimit());

        foreach ($unitIds as $row) {
            $configs[] = \Elements\OutputDataConfigToolkit\OutputDefinition::getByO_IdClassIdChannel($row['o_id'], $row['o_classId'], $row['channel']);
        }

        $this->model->setOutputDefinitions($configs);

        return $configs;
    }

    public function getTotalCount() {
        $amount = $this->db->fetchRow("SELECT COUNT(*) as amount FROM `" . \Elements\OutputDataConfigToolkit\OutputDefinition\Dao::TABLE_NAME . "`" . $this->getCondition());
        return $amount["amount"];
    }

}