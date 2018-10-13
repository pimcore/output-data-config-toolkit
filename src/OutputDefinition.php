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


namespace OutputDataConfigToolkitBundle;

use OutputDataConfigToolkitBundle\OutputDefinition\Dao;
use Pimcore\Logger;

class OutputDefinition extends \Pimcore\Model\AbstractModel {
    public $id;
    public $o_id;
    public $o_classId;
    public $channel;
    public $configuration;


    /**
     * @return OutputDefinition
     */
    public static function getByO_IdClassIdChannel($o_id, $classId, $channel) {
        $cacheKey = self::getCacheKey($o_id, $classId, $channel);

        try {
            $config = \Pimcore\Cache\Runtime::get($cacheKey);
        }
        catch (\Exception $e) {

            try {
                $config = new self();
                try {
                    $config->getDao()->getByO_IdClassIdChannel($o_id, $classId, $channel);
                    \Pimcore\Cache\Runtime::set($cacheKey, $config);
                } catch(\Exception $e) {
                    Logger::info($e->getMessage());
                    $config = null;
                }
            } catch(\Exception $ex) {
                Logger::debug($ex->getMessage());
                return null;
            }

        }

        return $config;
    }

    public static function getById($id) {
        try {
            $config = new self();
            $config->getDao()->getById($id);
            return $config;
        } catch(\Exception $ex) {
            Logger::debug($ex->getMessage());
            return null;
        }
    }

    private static function getCacheKey($o_id, $classId, $channel) {
        return Dao::TABLE_NAME . "_" . $o_id . "_" . $classId . "_" . $channel;
    }

    /**
     * @param array $values
     * @return OutputDefinition
     */
    public static function create($values = array()) {
        $config = new self();
        $config->setValues($values);
        return $config;
    }

    /**
     * @return void
     */
    public function save() {
        $this->getDao()->save();
    }

    /**
     * @return void
     */
    public function delete() {
        $cacheKey = self::getCacheKey($this->getO_id(), $this->getO_ClassId(), $this->getChannel());
        \Pimcore\Cache\Runtime::set($cacheKey, null);

        $this->getDao()->delete();
    }


    public function setChannel($channel) {
        $this->channel = $channel;
    }

    public function getChannel() {
        return $this->channel;
    }

    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public function setO_ClassId($o_classId) {
        $this->o_classId = $o_classId;
    }

    public function getO_ClassId() {
        return $this->o_classId;
    }

    public function setO_Id($o_id) {
        $this->o_id = $o_id;
    }

    public function getO_Id() {
        return $this->o_id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }


}
