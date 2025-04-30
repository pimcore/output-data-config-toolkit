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

class TableRow extends AbstractOperator
{
    protected $headline;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        $this->headline = $config->headline;
    }

    public function getLabeledValue($object)
    {
        $value = new \stdClass();

        $isEmpty = true;
        $childs = $this->getChilds();
        $valueArray = [];

        foreach ($childs as $c) {
            if ($c instanceof AbstractConfigElement) {
                $c->setClassificationstore($this->getClassificationstore());
                $c->setClassificationstoreGroup($this->getClassificationstoreGroup());
            }
            $col = $c->getLabeledValue($object);
            if (!empty($col) && (!$col->empty && !($c instanceof \OutputDataConfigToolkitBundle\ConfigElement\Operator\Text))) {
                $isEmpty = false;
            }
            $valueArray[] = $c->getLabeledValue($object);
        }

        $value->value = $valueArray;
        $value->headline = $this->headline;

        if ($isEmpty) {
            $value->empty = true;
        } else {
            $value->empty = false;
        }

        return $value;
    }
}
