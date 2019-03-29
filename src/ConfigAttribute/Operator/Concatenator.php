<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */
namespace OutputDataConfigToolkitBundle\ConfigAttribute\Operator;


use OutputDataConfigToolkitBundle\ConfigAttribute\AbstractConfigAttributeOperator;
use OutputDataConfigToolkitBundle\ConfigElement\Operator\Concatenator as ConcatenatorElement;
use OutputDataConfigToolkitBundle\Tools\Util;

class Concatenator extends AbstractConfigAttributeOperator
{
    /* @var string|null $glue */
    private $glue;

    /* @var bool $forceValue */
    private $forceValue;

    /* @var bool $formatNumbers */
    private $formatNumbers;

    /**
     * @return Concatenator
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
     * @return Concatenator
     */
    public function setGlue(?string $glue): Concatenator
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
     * @return Concatenator
     */
    public function setForceValue(bool $forceValue): Concatenator
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
     * @return Concatenator
     */
    public function setFormatNumbers(bool $formatNumbers): Concatenator
    {
        $this->formatNumbers = $formatNumbers;
        return $this;
    }


}
