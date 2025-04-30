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

class TranslateValue extends AbstractOperator
{
    private $prefix;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        $this->prefix = $config->prefix;
    }

    public function getLabeledValue($object)
    {
        $childs = $this->getChilds();
        if ($childs) {
            $translator = \Pimcore::getContainer()->get('translator');
            $c = $childs[0];
            if ($c instanceof AbstractConfigElement) {
                $c->setClassificationstore($this->getClassificationstore());
                $c->setClassificationstoreGroup($this->getClassificationstoreGroup());
            }
            $value = $c->getLabeledValue($object);
            if ($value->value) {
                $value->value = $translator->trans($this->prefix . $value->value);
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
