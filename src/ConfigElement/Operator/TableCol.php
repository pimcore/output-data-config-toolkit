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
 
class TableCol extends AbstractOperator {

    protected $colspan;
    protected $headline;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);

        $this->colspan = $config->colspan;
        $this->headline = $config->headline;
    }

    public function getLabeledValue($object) {
        $value = null;

        $childs = $this->getChilds();
        if($childs[0]) {
            $value = $childs[0]->getLabeledValue($object);
            $value->colSpan = $this->colspan;
            $value->headline = $this->headline;

            if(empty($value) || $childs[0] instanceof \OutputDataConfigToolkitBundle\ConfigElement\Operator\Text) {
                $value->empty = true;
            }            
        }

        return $value;
    }
}
