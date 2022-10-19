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

namespace OutputDataConfigToolkitBundle\ConfigAttribute;

/**
 * Class AbstractConfigAttribute
 *
 * @package OutputDataConfigToolkitBundle\ConfigAttribute
 */
abstract class AbstractConfigAttribute implements IConfigAttribute
{
    /* @var string|null $label */
    protected $label;

    /* @var string|null $type */
    protected $type;

    /* @var string|null $class */
    protected $class;

    /* @var AbstractConfigAttribute[] $childs */
    protected $childs;

    abstract public function applyDefaults();

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     *
     * @return $this
     */
    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return $this
     */
    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string|null $class
     *
     * @return $this
     */
    public function setClass(?string $class): static
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return IConfigAttribute[]
     */
    public function getChilds(): array
    {
        return isset($this->childs) ? $this->childs : [];
    }

    /**
     * @param AbstractConfigAttribute[] $childs
     *
     * @return $this
     */
    public function setChilds(array $childs): static
    {
        $this->childs = $childs;

        return $this;
    }
}
