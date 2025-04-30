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

class CellFormater extends AbstractOperator
{
    private $cssClass;

    private $styles;

    private $labelStyles;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        $this->cssClass = $config->cssClass;
        $this->styles = $config->styles;
        $this->label = $config->cssClass;
        $this->labelStyles = $config->labelStyles;
    }

    public function getLabeledValue($object)
    {
        $childs = $this->getChilds();
        if ($childs) {
            return $childs[0]->getLabeledValue($object);
        }

        return null;
    }

    public function getCssClass()
    {
        return $this->cssClass;
    }

    public function setStyles($styles)
    {
        $this->styles = $styles;
    }

    public function getStyles()
    {
        return $this->styles;
    }

    public function setLabelStyles($styles)
    {
        $this->labelStyles = $styles;
    }

    public function getLabelStyles()
    {
        return $this->labelStyles;
    }
}
