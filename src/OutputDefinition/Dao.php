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

namespace OutputDataConfigToolkitBundle\OutputDefinition;

use Doctrine\DBAL\Connection;
use OutputDataConfigToolkitBundle\OutputDefinition;

/**
 * @property OutputDefinition $model
 */
class Dao extends \Pimcore\Model\Dao\AbstractDao
{
    const TABLE_NAME = 'bundle_outputdataconfigtoolkit_outputdefinition';

    /**
     * Contains all valid columns in the database table
     *
     * @var array
     */
    protected $validColumns = [];

    /**
     * Get the valid columns from the database
     *
     * @return void
     */
    public function init()
    {
        $this->validColumns = $this->getValidTableColumns(self::TABLE_NAME);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function getByObjectIdClassIdChannel($id, $classId, $channel)
    {
        $outputDefinitionRaw = $this->db->fetchAssociative('SELECT * FROM ' . self::TABLE_NAME . ' WHERE objectId=? AND classId = ? AND channel = ?', [$id, $classId, $channel]);
        if (empty($outputDefinitionRaw)) {
            throw new \Exception('OutputDefinition ' . $id . ' - ' . $classId  . ' - ' . $channel . ' not found.');
        }
        $this->assignVariablesToModel($outputDefinitionRaw);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function getById($id)
    {
        $outputDefinitionRaw = $this->db->fetchAssociative('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id=?', [$id]);
        if (empty($outputDefinitionRaw)) {
            throw new \Exception('OutputDefinition-Id ' . $id . ' not found.');
        }
        $this->assignVariablesToModel($outputDefinitionRaw);
    }

    /**
     * Create a new record for the object in database
     *
     * @return void
     */
    public function create()
    {
        $class = get_object_vars($this->model);
        $data = [];

        foreach ($class as $key => $value) {
            if (in_array($key, $this->validColumns)) {
                if (is_array($value) || is_object($value)) {
                    $value = serialize($value);
                } elseif (is_bool($value)) {
                    $value = (int)$value;
                }
                $data[$key] = $value;
            }
        }
        $this->db->insert(self::TABLE_NAME, self::quoteDataIdentifiers($this->db, $data));
        $this->model->setId($this->db->lastInsertId());
    }

    /**
     * Save object to database
     *
     * @return void
     */
    public function save()
    {
        $other = OutputDefinition::getByObjectIdClassIdChannel($this->model->getObjectId(), $this->model->getClassId(), $this->model->getChannel());
        if ($other) {
            $this->model->setId($other->getId());
        }

        if ($this->model->getId()) {
            $this->update();
        } else {
            $this->create();
        }
    }

    /**
     * @return void
     */
    public function update()
    {
        $class = get_object_vars($this->model);
        $data = [];

        foreach ($class as $key => $value) {
            if (in_array($key, $this->validColumns)) {
                if (is_array($value) || is_object($value)) {
                    $value = serialize($value);
                } elseif (is_bool($value)) {
                    $value = (int)$value;
                }
                $data[$key] = $value;
            }
        }
        $this->db->update(self::TABLE_NAME, self::quoteDataIdentifiers($this->db, $data), ['id' => $this->model->getId()]);
    }

    /**
     * Deletes object from database
     *
     * @return void
     */
    public function delete()
    {
        $this->db->delete(self::TABLE_NAME, ['id' => (int) $this->model->getId()]);
    }

    public static function quoteDataIdentifiers(Connection $db, array $data): array
    {
        $newData = [];
        foreach ($data as $key => $value) {
            $newData[$db->quoteIdentifier($key)] = $value;
        }

        return $newData;
    }
}
