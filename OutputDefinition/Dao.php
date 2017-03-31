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


namespace OutputDataConfigToolkitBundle\OutputDefinition;


use OutputDataConfigToolkitBundle\OutputDefinition;

class Dao extends \Pimcore\Model\Dao\AbstractDao {

    const TABLE_NAME = "bundle_outputdataconfigtoolkit_outputdefinition";

    /**
     * Contains all valid columns in the database table
     *
     * @var array
     */
    protected $validColumns = array();

    /**
     * Get the valid columns from the database
     *
     * @return void
     */
    public function init() {
        $this->validColumns = $this->getValidTableColumns(self::TABLE_NAME);
    }


    /**
     * @return void
     */
    public function getByO_IdClassIdChannel($o_id, $classId, $channel) {

        $outputDefinitionRaw = $this->db->fetchRow("SELECT * FROM " . self::TABLE_NAME . " WHERE o_id=? AND o_classId = ? AND channel = ?", array($o_id, $classId, $channel));
        if(empty($outputDefinitionRaw)) {
            throw new \Exception("OutputDefinition " . $o_id . " - " . $classId  . " - " . $channel . " not found.");
        }
        $this->assignVariablesToModel($outputDefinitionRaw);
    }

    /**
     * @return void
     */
    public function getById($id) {
        $outputDefinitionRaw = $this->db->fetchRow("SELECT * FROM " . self::TABLE_NAME . " WHERE id=?", array($id));
        if(empty($outputDefinitionRaw)) {
            throw new \Exception("OutputDefinition-Id " . $id . " not found.");
        }
        $this->assignVariablesToModel($outputDefinitionRaw);
    }


    /**
     * Create a new record for the object in database
     *
     * @return boolean
     */
    public function create() {
        $this->db->insert(self::TABLE_NAME, array());
        $this->model->setId($this->db->lastInsertId());

        $this->save();
    }

    /**
     * Save object to database
     *
     * @return void
     */
    public function save() {
        $other = OutputDefinition::getByO_IdClassIdChannel($this->model->getO_Id(), $this->model->getO_ClassId(), $this->model->getChannel());
        if($other) {
            $this->model->setId($other->getId());
        }
        
        if ($this->model->getId()) {
            return $this->update();
        }
        return $this->create();
    }

    /**
     * @return void
     */
    public function update() {

        $class = get_object_vars($this->model);

        foreach ($class as $key => $value) {
            if (in_array($key, $this->validColumns)) {

                if (is_array($value) || is_object($value)) {
                    $value = serialize($value);
                } else  if(is_bool($value)) {
                    $value = (int)$value;
                }
                $data[$key] = $value;
            }
        }
        $this->db->updateWhere(self::TABLE_NAME, $data, "id=" . $this->db->quote($this->model->getId()));
    }

    /**
     * Deletes object from database
     *
     * @return void
     */
    public function delete() {
        $this->db->deleteWhere(self::TABLE_NAME, "id=" . $this->db->quote($this->model->getId()));
    }

}
