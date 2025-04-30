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

namespace OutputDataConfigToolkitBundle\OutputDefinition\Listing;

use Doctrine\DBAL\Exception;
use OutputDataConfigToolkitBundle\OutputDefinition;

/**
 * Class Dao
 *
 * @package OutputDataConfigToolkitBundle\OutputDefinition\Listing
 *
 * @property OutputDefinition\Listing $model
 */
class Dao extends \Pimcore\Model\Listing\Dao\AbstractDao
{
    /**
     * @throws Exception
     */
    public function load(): array
    {
        $configs = [];

        $params = array_column($this->model->getConditionParams() ?: [], 'value');

        $unitIds = $this->db->fetchAllAssociative(
            'SELECT objectId, id, classId, ' . $this->db->quoteIdentifier('channel') . ' FROM ' . OutputDefinition\Dao::TABLE_NAME .
            $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(),
            $params
        );

        foreach ($unitIds as $row) {
            $configs[] = OutputDefinition::getByObjectIdClassIdChannel($row['objectId'], $row['classId'], $row['channel']);
        }

        $this->model->setOutputDefinitions($configs);

        return $configs;
    }

    public function getTotalCount(): int
    {
        $params = array_column($this->model->getConditionParams() ?: [], 'value');
        $amount = $this->db->fetchAssociative('SELECT COUNT(*) as amount FROM `' . OutputDefinition\Dao::TABLE_NAME . '`' . $this->getCondition(), $params);

        return (int)$amount['amount'];
    }
}
