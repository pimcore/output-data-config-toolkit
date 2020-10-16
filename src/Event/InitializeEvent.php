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


use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class ObjectEvent
 */
class InitializeEvent extends GenericEvent
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
     * @return InitializeEvent
     */
    public function setObject(AbstractObject $object): self
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
