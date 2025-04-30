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

namespace OutputDataConfigToolkitBundle\Event;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ObjectEvent
 */
class InitializeEvent extends Event
{
    /**
     * @var AbstractObject
     */
    private $object;

    /**
     * @var bool
     */
    private $hideConfigTab;

    /**
     * ObjectEvent constructor.
     *
     * @param AbstractObject $object
     */
    public function __construct(AbstractObject $object)
    {
        $this->object = $object;
        $this->hideConfigTab = false;
    }

    /**
     * @return AbstractObject
     */
    public function getObject(): AbstractObject
    {
        return $this->object;
    }

    /**
     * @param AbstractObject $object
     *
     * @return $this
     */
    public function setObject(AbstractObject $object): static
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHideConfigTab(): bool
    {
        return $this->hideConfigTab;
    }

    /**
     * @param bool $hideConfigTab
     *
     * @return $this
     */
    public function setHideConfigTab(bool $hideConfigTab): static
    {
        $this->hideConfigTab = $hideConfigTab;

        return $this;
    }
}
