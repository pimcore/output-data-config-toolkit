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


namespace OutputDataConfigToolkitBundle\ConfigElement\Value;

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
                $formatter = \Pimcore::getContainer()->get("pimcore.locale.intl_formatter");
                $value = $formatter->formatNumber($value);
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
