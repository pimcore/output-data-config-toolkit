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

namespace OutputDataConfigToolkitBundle;

use OutputDataConfigToolkitBundle\OutputDefinition\Dao;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Logger;

/**
 * @method OutputDefinition\Dao getDao()
 */
class OutputDefinition extends \Pimcore\Model\AbstractModel
{
    public $id;
    public $objectId;
    public $classId;
    public $channel;
    public $configuration;

    /**
     * @return OutputDefinition|null
     */
    public static function getByObjectIdClassIdChannel($objectId, $classId, $channel)
    {
        $cacheKey = self::getCacheKey($objectId, $classId, $channel);

        try {
            $config = RuntimeCache::get($cacheKey);
        } catch (\Exception $e) {
            try {
                $config = new self();
                try {
                    $config->getDao()->getByObjectIdClassIdChannel($objectId, $classId, $channel);
                    RuntimeCache::set($cacheKey, $config);
                } catch (\Exception $e) {
                    Logger::info($e->getMessage());
                    $config = null;
                }
            } catch (\Exception $ex) {
                Logger::debug($ex->getMessage());

                return null;
            }
        }

        return $config;
    }

    public static function getById($id)
    {
        try {
            $config = new self();
            $config->getDao()->getById($id);

            return $config;
        } catch (\Exception $ex) {
            Logger::debug($ex->getMessage());

            return null;
        }
    }

    private static function getCacheKey($objectId, $classId, $channel)
    {
        return Dao::TABLE_NAME . '_' . $objectId . '_' . $classId . '_' . $channel;
    }

    /**
     * @param array $values
     *
     * @return OutputDefinition
     */
    public static function create($values = [])
    {
        $config = new self();
        $config->setValues($values);

        return $config;
    }

    /**
     * @return void
     */
    public function save()
    {
        $this->getDao()->save();
    }

    /**
     * @return void
     */
    public function delete()
    {
        $cacheKey = self::getCacheKey($this->getObjectId(), $this->getClassId(), $this->getChannel());
        RuntimeCache::set($cacheKey, null);

        $this->getDao()->delete();
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setClassId($classId)
    {
        $this->classId = $classId;
    }

    public function getClassId()
    {
        return $this->classId;
    }

    public function setObjectId($id)
    {
        $this->objectId = $id;
    }

    public function getObjectId()
    {
        return $this->objectId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
