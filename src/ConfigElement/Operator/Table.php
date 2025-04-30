<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace OutputDataConfigToolkitBundle\ConfigElement\Operator;

use OutputDataConfigToolkitBundle\ConfigElement\AbstractConfigElement;

class Table extends AbstractOperator
{
    protected $tooltip;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);
        $this->tooltip = $config->tooltip;
    }

    public function getLabeledValue($object)
    {
        $value = new \stdClass();

        $childs = $this->getChilds();

        $value->label = $this->label;
        $value->type = 'Operator_Table';

        $isEmpty = false;
        $valueArray = [];
        foreach ($childs as $c) {
            if ($c instanceof AbstractConfigElement) {
                $c->setClassificationstore($this->getClassificationstore());
                $c->setClassificationstoreGroup($this->getClassificationstoreGroup());
            }
            $row = $c->getLabeledValue($object);
            $valueArray[] = $row;
            $isEmpty = $row->empty;
        }
        $value->empty = $isEmpty;
        $value->value = $valueArray;
        $value->tooltip = $this->tooltip;

        return $value;
    }

    public function getTooltip()
    {
        return $this->tooltip;
    }
}
