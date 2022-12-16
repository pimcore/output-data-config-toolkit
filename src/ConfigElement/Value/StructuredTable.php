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

namespace OutputDataConfigToolkitBundle\ConfigElement\Value;

class StructuredTable extends DefaultValue
{
    protected $wholeTable = true;
    protected $row;
    protected $col;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);

        $this->wholeTable = $config->wholeTable;
        $this->row = $config->row;
        $this->col = $config->col;
    }

    public function getLabeledValue($object)
    {
        if ($this->wholeTable) {
            return parent::getLabeledValue($object);
        } elseif (!empty($this->row) && !empty($this->col)) {
            $wholeResult = parent::getLabeledValue($object);

            $label = $wholeResult->label;
            $value = null;

            if (!empty($wholeResult->value)) {
                $getter = 'get' . ucfirst($this->row . '__' . $this->col);
                $value = $wholeResult->value->$getter();
            }

            $result = new \stdClass();
            $result->label = $label;

            if (is_numeric($value)) {
                $formatter = \Pimcore::getContainer()->get(\Pimcore\Localization\IntlFormatter::class);
                $value = $formatter->formatNumber($value);
            }

            $result->value = $value;
            $result->def = $wholeResult->def ?? null;

            if (empty($result->value)) {
                $result->empty = true;
            } else {
                $result->empty = false;
            }

            return $result;
        } else {
            throw new \Exception('Invalid Configuration of StructuredTable ConfigElement');
        }
    }
}
