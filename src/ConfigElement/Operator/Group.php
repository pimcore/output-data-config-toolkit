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

class Group extends AbstractOperator
{
    public function getLabeledValue($object)
    {
        $valueArray = [];

        $childs = $this->getChilds();
        foreach ($childs as $c) {
            if ($c instanceof AbstractConfigElement) {
                $c->setClassificationstore($this->getClassificationstore());
                $c->setClassificationstoreGroup($this->getClassificationstoreGroup());
            }
            $value = $c->getLabeledValue($object);
            if (!empty($value) && (!property_exists($value, 'empty') || !$value->empty) && (!method_exists($value, 'isEmpty') || !$value->isEmpty())) {
                $valueArray[] = $value;
            }
        }

        if (!empty($valueArray)) {
            return $valueArray;
        }

        return null;
    }
}
