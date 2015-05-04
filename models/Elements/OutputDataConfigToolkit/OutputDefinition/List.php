<?php

class Elements_OutputDataConfigToolkit_OutputDefinition_List extends Pimcore_Model_List_Abstract {

    /**
     * @var array
     */
    public $outputDefinitions;

    /**
     * @var array
     */
    public function isValidOrderKey($key) {
        if($key == "o_id" || $key == "o_classId" || $key == "channel") {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    function getOutputDefinitions() {
        if(empty($this->outputDefinitions)) {
            $this->load();
        }
        return $this->outputDefinitions;
    }

    /**
     * @param array $units
     * @return void
     */
    function setOutputDefinitions($outputDefinitions) {
        $this->outputDefinitions = $outputDefinitions;
    }

}
