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

namespace OutputDataConfigToolkitBundle\Event;


use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ObjectEvent
 */
class InitializeEvent extends Event
{
    /**
     * @var Concrete
     */
    private $object;

    /**
     * @var bool
     */
    private $hideConfigTab;

    /**
     * ObjectEvent constructor.
     * @param Concrete $object
     */
    public function __construct(Concrete $object)
    {
        $this->object = $object;
        $this->hideConfigTab = false;
    }

    /**
     * @return Concrete
     */
    public function getObject(): Concrete
    {
        return $this->object;
    }

    /**
     * @param Concrete $object
     * @return InitializeEvent
     */
    public function setObject(Concrete $object): self
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
     * @return InitializeEvent
     */
    public function setHideConfigTab(bool $hideConfigTab): self
    {
        $this->hideConfigTab = $hideConfigTab;
        return $this;
    }

}
