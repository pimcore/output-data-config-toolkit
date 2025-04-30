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

namespace OutputDataConfigToolkitBundle\ConfigElement;

abstract class AbstractConfigElement implements IConfigElement
{
    protected $attribute;

    protected $label;

    protected $context;

    /** @var string|null */
    public ?string $classificationstore = null;

    /** @var string|null */
    public ?string $classificationstore_group = null;

    public function __construct($config, $context = null)
    {
        $this->attribute = $config->attribute ?? null;
        $this->label = $config->label;

        $this->context = $context;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getClassificationstore(): ?string
    {
        return $this->classificationstore;
    }

    public function getClassificationstoreGroup(): ?string
    {
        return $this->classificationstore_group;
    }

    public function setClassificationstore(?string $classificationstore): void
    {
        $this->classificationstore = $classificationstore;
    }

    public function setClassificationstoreGroup(?string $classificationstore_group): void
    {
        $this->classificationstore_group = $classificationstore_group;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }
}
