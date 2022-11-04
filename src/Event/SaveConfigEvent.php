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

namespace OutputDataConfigToolkitBundle\Event;

use OutputDataConfigToolkitBundle\OutputDefinition;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class SaveConfigEvent
 */
class SaveConfigEvent extends Event
{
    private $config;

    private $sortAttributes = false;

    public function __construct(OutputDefinition $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function doSortAttributes(): bool
    {
        return $this->sortAttributes;
    }

    /**
     * @param bool $sortAttributes
     *
     * @return $this
     */
    public function setSortAttributes(bool $sortAttributes): static
    {
        $this->sortAttributes = $sortAttributes;

        return $this;
    }

    public function getConfig(): OutputDefinition
    {
        return $this->config;
    }
}
