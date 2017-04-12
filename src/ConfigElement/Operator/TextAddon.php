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


namespace OutputDataConfigToolkitBundle\ConfigElement\Operator;
 
class TextAddon extends AbstractOperator {

    private $addon;

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);

        $this->addon = $config->addon;
    }

    public function getLabeledValue($object) {
        $childs = $this->getChilds();
        if($childs[0]) {
            $value = $childs[0]->getLabeledValue($object);

            if(!is_null($value->value)) {
                $value->value = $value->value.$this->getAddon();
            }

            return $value;
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getAddon()
    {
        return $this->addon;
    }

    /**
     * @param mixed $prefix
     */
    public function setAddon($addon)
    {
        $this->addon = $addon;
    }



}
