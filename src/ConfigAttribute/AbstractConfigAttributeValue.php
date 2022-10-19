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

use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

/**
 * Class AbstractConfigAttributeValue
 *
 * @package OutputDataConfigToolkitBundle\ConfigAttribute
 */
abstract class AbstractConfigAttributeValue extends AbstractConfigAttribute
{
    /* @var string|null $attribute */
    protected $attribute;

    /* @var string|null $dataType */
    protected $dataType;

    /* @var string|null $icon */
    protected $icon;

    /* @var string|null $text */
    protected $text;

    /**
     * @param KeyConfig $keyConfig
     *
     * @return AbstractConfigAttributeValue
     */
    public function applyFromClassificationKeyConfig(KeyConfig $keyConfig)
    {
        return $this
            ->setAttribute('#cs#' . implode('#', [$keyConfig->getId(), $keyConfig->getName()]))
            ->setText($keyConfig->getName())
            ->setDataType($keyConfig->getType());
    }

    /**
     * @return string|null
     */
    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    /**
     * @param string|null $attribute
     *
     * @return $this
     */
    public function setAttribute(?string $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    /**
     * @param string|null $dataType
     *
     * @return $this
     */
    public function setDataType(?string $dataType): static
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     *
     * @return $this
     */
    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
     * @return $this
     */
    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }
}
