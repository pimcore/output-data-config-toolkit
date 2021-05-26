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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace AppBundle\EventListener;

use OutputDataConfigToolkitBundle\Event\InitializeEvent;
use OutputDataConfigToolkitBundle\Event\OutputDataConfigToolkitEvents;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Product;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OutputDataConfigToolkitListener
 * @package AppBundle\EventListener
 */
class OutputDataConfigToolkitListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            OutputDataConfigToolkitEvents::INITIALIZE => "onInitialize"
        ];
    }

    /**
     * Manipulate outputdataconfigtoolkit initialization
     *      - only show output config tab for product objects
     *      - always use product root
     *
     * @param InitializeEvent $event
     */
    public function onInitialize(InitializeEvent $event)
    {
        $object = $event->getObject();
        if (!$object instanceof Concrete || $object->getClassName() != substr(strrchr(Product::class, "\\"), 1)) {
            $event->setHideConfigTab(true);
        } else {
            while (
                $object->getParentId() != 1
                && !$object->getParent() instanceof Folder
                && ($object = $object->getParent())
            );
        }
        $event->setObject($object);
    }
}
