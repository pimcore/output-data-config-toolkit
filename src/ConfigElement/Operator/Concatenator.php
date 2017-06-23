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


namespace OutputDataConfigToolkitBundle\ConfigElement\Operator;

class Concatenator extends AbstractOperator {

    protected $glue;
    protected $forceValue;
    protected $formatNumbers;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);
        $this->glue = $config->glue;
        $this->forceValue = $config->forceValue;
        $this->formatNumbers = $config->formatNumbers;
    }

    public function getLabeledValue($object) {
        $result = new \stdClass();
        $result->label = $this->label;

        $hasValue = true;
        if(!$this->forceValue) {
            $hasValue = false;
        }


        $childs = $this->getChilds();
        $valueArray = [];

        foreach($childs as $c) {
            $value = $c->getLabeledValue($object)->value;

            if(!$hasValue) {
                if(!empty($value) || ((method_exists($value, "isEmpty") && !$value->isEmpty()))) {
                    $hasValue = true;
                }
            }

            if($this->formatNumbers && is_numeric($value)) {
                $formattingService = \Pimcore::getContainer()->get('pimcore.locale.intl_formatter');
                $value = $formattingService->formatNumber($value);
            }

            if($value !== null) {
                $valueArray[] = $value;
            }
        }

        if($hasValue) {
            $result->value = implode($this->glue, $valueArray);
            return $result;
        } else {
            $result->empty = true;
            return $result;
        }
    }
}
