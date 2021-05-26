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

/**
 * Class OutputDataConfigToolkitEvents
 *
 * @package OutputDataConfigToolkitBundle\Controller
 */
class OutputDataConfigToolkitEvents
{
    /**
     * @Event("OutputDataConfigToolkitBundle\Event\InitializeEvent")
     *
     * @var string
     */
    const INITIALIZE = 'outputDataConfigToolkit.initialize';

    /**
     * @Event("OutputDataConfigToolkitBundle\Event\SaveConfigEvent")
     *
     * @var string
     */
    const SAVE_CONFIG_EVENT = 'outputDataConfigToolkit.saveEvent';
}
