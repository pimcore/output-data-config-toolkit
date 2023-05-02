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

use OutputDataConfigToolkitBundle\ConfigElement\AbstractConfigElement;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\DefaultMockup;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick\Definition;
use Pimcore\Model\DataObject\Service;

class DefaultValue extends AbstractConfigElement
{
    /** @var string|null */
    protected $icon;

    /** @var string|null */
    public $classificationstore;

    /** @var string|null */
    public $classificationstore_group;

    public function __construct($config, $context = null)
    {
        parent::__construct($config, $context);
        $this->icon = $config->icon ?? null;
    }

    public function getLabeledValue($object)
    {
        $attributeParts = explode('~', $this->attribute);
        $label = $this->label;

        $getter = 'get' . ucfirst($this->attribute);

        $brickType = null;
        $brickKey = null;
        $brickInfos = null;
        $brickfield = null;
        $brickTypeGetter = null;
        if (substr($this->attribute, 0, 1) == '~') {
            // key value, ignore for now
        } elseif (count($attributeParts) > 1) {
            $brickType = $attributeParts[0];
            $brickKey = $attributeParts[1];

            if ($brickKey && strpos($brickType, '?') === 0) {
                $definitionJson = substr($brickType, 1);
                $brickInfos = json_decode($definitionJson);
                $containerKey = $brickInfos->containerKey;
                $fieldName = $brickInfos->fieldname;
                $brickfield = $brickInfos->brickfield;

                $brickType = $containerKey;

                $getter = 'get'.ucfirst($fieldName);
                $brickTypeGetter = 'get'.ucfirst($brickType);
                $brickGetter = 'get'.ucfirst($brickfield);
            } elseif ($object instanceof Concrete) {
                $getter = 'get' . ucfirst(Service::getFieldForBrickType($object->getClass(), $brickType));
                $brickTypeGetter = 'get' . ucfirst($brickType);
                $brickGetter = 'get' . ucfirst($brickKey);
            }
        } elseif (substr($this->attribute, 0, 4) == '#cs#') {
            // checking classification store fieldname
            if (!$this->classificationstore) {
                return null;
            }
            $getter = 'get' . ucfirst($this->classificationstore);
            // checking classification sote group
            if (!$this->classificationstore_group) {
                return null;
            }
            $groupDef = Classificationstore\GroupConfig::getByName($this->classificationstore_group);

            // classification store
            $attribute = str_replace('#cs#', '', $this->attribute);
            list($keyId) = explode('#', $attribute);

            $value = $object->$getter()->getLocalizedKeyValue($groupDef->getId(), $keyId);

            $result = new \stdClass();
            $result->value = $value;
            $result->label = $this->label;
            $result->attribute = $this->attribute;

            $config = Classificationstore\KeyConfig::getById((int) $keyId);
            if ($config) {
                $result->def = Classificationstore\Service::getFieldDefinitionFromKeyConfig($config);
            }

            if (empty($value) || (is_object($value) && method_exists($value, 'isEmpty') && $value->isEmpty())) {
                $result->empty = true;
            } else {
                $result->empty = false;
            }

            return $result;
        }
        if (method_exists($object, $getter)
            || (class_exists(DefaultMockup::class) && $object instanceof DefaultMockup)) {
            $value = $object->$getter();
            $def = null;

            if ((class_exists(DefaultMockup::class) && $object instanceof DefaultMockup)
                || $object instanceof AbstractObject) {
                if ($object instanceof Concrete) {
                    $class = $object->getClass();
                    $def = $class->getFieldDefinition($this->attribute);
                    if (!$def) {
                        $lf = $class->getFieldDefinition('localizedfields');
                        if ($lf instanceof Localizedfields) {
                            $def = $lf->getFieldDefinition($this->attribute);
                        }
                    }
                }

                if (empty($label)) {
                    if ($def) {
                        $label = $def->getTitle();
                    }
                }

                if (!empty($value) && !empty($brickGetter)) {
                    $brickDef = Definition::getByKey($brickType);
                    $def = $brickDef->getFieldDefinition($brickKey);
                    if (!$def) {
                        $lf = $brickDef->getFieldDefinition('localizedfields');
                        if ($lf instanceof Localizedfields) {
                            $def = $lf->getFieldDefinition($brickInfos ? $brickfield : $this->attribute);
                        }
                    }

                    if (empty($label)) {
                        if ($def) {
                            $label = $def->getTitle();
                        }
                    }

                    if (is_object($value) && method_exists($value, $brickTypeGetter)) {
                        $value = $value->$brickTypeGetter();

                        if (is_object($value) && method_exists($value, $brickGetter)) {
                            $value = $value->$brickGetter();
                        } elseif ($brickInfos && !is_null($value)) {
                            $lfs = $value->getLocalizedfields();
                            $value = $lfs->getLocalizedValue($brickfield);
                        } else {
                            $value = null;
                        }
                    } else {
                        $value = null;
                    }
                }
            }

            $result = new \stdClass();
            $result->value = $value;
            $result->label = $label;

            if (empty($value) || (is_object($value) && method_exists($value, 'isEmpty') && $value->isEmpty())) {
                $result->empty = true;
            } else {
                $result->empty = false;
            }

            $result->def = $def;

            return $result;
        }

        return null;
    }
}
