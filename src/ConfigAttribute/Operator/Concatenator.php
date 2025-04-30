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

namespace OutputDataConfigToolkitBundle\ConfigAttribute\Operator;

use OutputDataConfigToolkitBundle\ConfigAttribute\AbstractConfigAttributeOperator;
use OutputDataConfigToolkitBundle\ConfigElement\Operator\Concatenator as ConcatenatorElement;
use OutputDataConfigToolkitBundle\Tools\Util;

class Concatenator extends AbstractConfigAttributeOperator
{
    // @var string|null $glue
    private $glue;

    // @var bool $forceValue
    private $forceValue;

    // @var bool $formatNumbers
    private $formatNumbers;

    /**
     * @return $this
     */
    public function applyDefaults()
    {
        return parent::applyDefaults()
            ->setClass(Util::getClassName(ConcatenatorElement::class));
    }

    /**
     * @return string|null
     */
    public function getGlue(): ?string
    {
        return $this->glue;
    }

    /**
     * @param string|null $glue
     *
     * @return $this
     */
    public function setGlue(?string $glue): static
    {
        $this->glue = $glue;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForceValue(): bool
    {
        return $this->forceValue;
    }

    /**
     * @param bool $forceValue
     *
     * @return $this
     */
    public function setForceValue(bool $forceValue): static
    {
        $this->forceValue = $forceValue;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFormatNumbers(): bool
    {
        return $this->formatNumbers;
    }

    /**
     * @param bool $formatNumbers
     *
     * @return $this
     */
    public function setFormatNumbers(bool $formatNumbers): static
    {
        $this->formatNumbers = $formatNumbers;

        return $this;
    }
}
