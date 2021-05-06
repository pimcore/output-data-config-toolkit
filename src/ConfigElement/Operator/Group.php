<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OutputDataConfigToolkitBundle\ConfigElement\Operator;

class Group extends AbstractOperator
{
    public function getLabeledValue($object)
    {
        $valueArray = [];

        $childs = $this->getChilds();
        foreach ($childs as $c) {
            $value = $c->getLabeledValue($object);
            if (!empty($value) && !$value->empty && (!method_exists($value, 'isEmpty') || !$value->isEmpty())) {
                $valueArray[] = $value;
            }
        }

        if (!empty($valueArray)) {
            return $valueArray;
        }

        return null;
    }
}
