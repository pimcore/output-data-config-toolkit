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

class TextAddon extends AbstractOperator
{
    private $addon;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        $this->addon = $config->addon;
    }

    public function getLabeledValue($object)
    {
        $childs = $this->getChilds();
        if (!empty($childs)) {
            $c = $childs[0];
            if ($c instanceof AbstractConfigElement) {
                $c->setClassificationstore($this->getClassificationstore());
                $c->setClassificationstoreGroup($this->getClassificationstoreGroup());
            }
            $value = $c->getLabeledValue($object);

            if (!is_null($value->value)) {
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
     * @param mixed $addon
     */
    public function setAddon($addon)
    {
        $this->addon = $addon;
    }
}
