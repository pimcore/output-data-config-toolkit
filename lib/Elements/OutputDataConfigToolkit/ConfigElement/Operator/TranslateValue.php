<?php
namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;
 
class TranslateValue extends AbstractOperator {

    private $prefix;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);

        $this->prefix = $config->prefix;
    }

    public function getLabeledValue(\Object_Abstract $object) {
        $childs = $this->getChilds();
        if($childs[0]) {
            $translate = new \Pimcore_Translate_Website(\Zend_Registry::get('Zend_Locale'));

            $value = $childs[0]->getLabeledValue($object);
            if($value->value) {
                $value->value = $translate->translate($this->prefix . $value->value);
            }

            return $value;
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }



}
