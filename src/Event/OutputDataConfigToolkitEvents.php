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

    /**
     * @Event("OutputDataConfigToolkitBundle\Event\GroupClassificationStoreEvent")
     *
     * @var string
     */
    const GROUP_CLASSIFICATION_STORE_EVENT = 'outputDataConfigToolkit.groupClassificationStoreEvent';
}
