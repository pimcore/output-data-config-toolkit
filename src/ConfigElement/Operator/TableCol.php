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

class TableCol extends AbstractOperator
{
    protected $colspan;

    protected $headline;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        $this->colspan = $config->colspan;
        $this->headline = $config->headline;
    }

    public function getLabeledValue($object)
    {
        $value = null;

        $childs = $this->getChilds();
        if ($childs) {
            $c = $childs[0];
            if ($c instanceof AbstractConfigElement) {
                $c->setClassificationstore($this->getClassificationstore());
                $c->setClassificationstoreGroup($this->getClassificationstoreGroup());
            }
            $value = $c->getLabeledValue($object);
            $value->colSpan = $this->colspan;
            $value->headline = $this->headline;

            if (empty($value) || $childs[0] instanceof \OutputDataConfigToolkitBundle\ConfigElement\Operator\Text) {
                $value->empty = true;
            }
        }

        return $value;
    }
}
