<?php

class Elements_OutputDataConfigToolkit_OutputDefinition extends Pimcore_Model_Abstract {
    public $id;
    public $o_id;
    public $o_classId;
    public $channel;
    public $configuration;


    /**
     * @return Elements_OutputDataConfigToolkit_OutputDefinition
     */
    public static function getByO_IdClassIdChannel($o_id, $classId, $channel) {
        $cacheKey = self::getCacheKey($o_id, $classId, $channel);

        try {
            $config = Zend_Registry::get($cacheKey);
        }
        catch (Exception $e) {

            try {
                $config = new self();
                try {
                    $config->getResource()->getByO_IdClassIdChannel($o_id, $classId, $channel);
                } catch(Exception $e) {
                    Logger::info($e->getMessage());
                    $config = null;
                }
                Zend_Registry::set($cacheKey, $config);
            } catch(Exception $ex) {
                Logger::debug($ex->getMessage());
                return null;
            }

        }

        return $config;
    }

    public static function getById($id) {
        try {
            $config = new self();
            $config->getResource()->getById($id);
            return $config;
        } catch(Exception $ex) {
            Logger::debug($ex->getMessage());
            return null;
        }        
    }

    private static function getCacheKey($o_id, $classId, $channel) {
        return Elements_OutputDataConfigToolkit_OutputDefinition_Resource::TABLE_NAME . "_" . $o_id . "_" . $classId . "_" . $channel;
    }

    /**
     * @param array $values
     * @return Elements_OutputDataConfigToolkit_OutputDefinition
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
        $this->getResource()->save();
    }

    /**
     * @return void
     */
    public function delete() {
        $cacheKey = self::getCacheKey($this->getO_id(), $this->getO_ClassId(), $this->getChannel());
        Zend_Registry::set($cacheKey, null);

        $this->getResource()->delete();
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
