<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Value;

use Elements\OutputDataConfigToolkit\ConfigElement as ConfigElement;

class StructuredTable extends DefaultValue {

    protected $wholeTable = true;
    protected $row;
    protected $col;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);

        $this->wholeTable = $config->wholeTable;
        $this->row = $config->row;
        $this->col = $config->col;
    }


    public function getLabeledValue($object) {
        if($this->wholeTable) {
            return parent::getLabeledValue($object);
        } else if(!empty($this->row) && !empty($this->col)) {

            $wholeResult = parent::getLabeledValue($object);

            $label = $wholeResult->label;




            if(!empty($wholeResult->value)) {
                $getter = "get" . ucfirst($this->row . "__" . $this->col);
                $value = $wholeResult->value->$getter();
            }


            $result = new \stdClass();
            $result->label = $label;

            if(is_numeric($value)) {
                $value = \Zend_Locale_Format::toNumber($value, array('locale' => \Zend_Registry::get("Zend_Locale")));
            }

            $result->value = $value;
            $result->def = $wholeResult->def;

            if(empty($result->value)) {
                $result->empty = true;
            } else {
                $result->empty = false;
            }

            return $result;
        } else {
            throw new \Exception("Invalid Configuration of StructuredTable ConfigElement");
        }
    }

}
