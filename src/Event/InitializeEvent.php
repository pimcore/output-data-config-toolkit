<?php

namespace OutputDataConfigToolkitBundle\Event;


use Pimcore\Model\DataObject\Concrete;

/**
 * Class ObjectEvent
 */
class InitializeEvent extends \Symfony\Component\EventDispatcher\Event
{
    /**
     * @var Concrete
     */
    private $object;

    /**
     * @var bool
     */
    private $doNotShowConfigTab;

    /**
     * ObjectEvent constructor.
     * @param Concrete $object
     */
    public function __construct(Concrete $object)
    {
        $this->object = $object;
        $this->doNotShowConfigTab = false;
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
     * @return ObjectEvent
     */
    public function setObject(Concrete $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDoNotShowConfigTab(): bool
    {
        return $this->doNotShowConfigTab;
    }

    /**
     * @param bool $doAbortInitialization
     * @return Initialize
     */
    public function setDoNotShowConfigTab(bool $doNotShowConfigTab): self
    {
        $this->doNotShowConfigTab = $doNotShowConfigTab;
        return $this;
    }

}
