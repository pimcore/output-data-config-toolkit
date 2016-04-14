<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;
 
class TextAddon extends AbstractOperator {

    private $addon;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);

        $this->addon = $config->addon;
    }

    public function getLabeledValue($object) {
        $childs = $this->getChilds();
        if($childs[0]) {
            $value = $childs[0]->getLabeledValue($object);

            if(!is_null($value->value)) {
                $value->value = $value->value.$this->getAddon();
            }

            return $value;
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getAddon()
    {
        return $this->addon;
    }

    /**
     * @param mixed $prefix
     */
    public function setAddon($addon)
    {
        $this->addon = $addon;
    }



}
