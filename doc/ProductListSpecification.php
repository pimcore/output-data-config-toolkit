<?php

namespace AppBundle\Templating\Helper;


use OutputDataConfigToolkitBundle\ConfigElement\Operator\Concatenator;
use OutputDataConfigToolkitBundle\ConfigElement\Operator\Group;
use OutputDataConfigToolkitBundle\ConfigElement\Value\DefaultValue;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Checkbox;
use Pimcore\Model\DataObject\ClassDefinition\Data\Image;
use Pimcore\Model\DataObject\ClassDefinition\Data\Multiselect;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation;
use Pimcore\Model\DataObject\ClassDefinition\Data\Select;
use Pimcore\Translation\Translator;
use Symfony\Component\Templating\Helper\Helper;

class ProductDetailSpecification extends Helper {

    /**
     * @var Translator
     */
    protected $translator;


    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'productDetailSpecification';
    }


    public function __invoke($property, $product) {
        if($property instanceof Group) {
            $labeledValue = $property->getLabeledValue($product);
            if($labeledValue) {
                $result = "
                            <tr class='groupheading' >
                                <th colspan='2'>" . $this->translator->trans("attr." . $property->getLabel()) . "</th>
                            </tr>
                ";

                foreach($property->getChilds() as $child) {

                    $result .= $this->__invoke($child, $product);

                }

                return $result;
            }

        } else if($property instanceof DefaultValue ||
            $property instanceof Concatenator) {
            $labeledValue = $property->getLabeledValue($product);
            if($labeledValue->def instanceof Select) {
                $value = $this->getSelectValue($labeledValue->def, $labeledValue->value);
            } else if($labeledValue->def instanceof Multiselect) {

                $values = $labeledValue->value;
                $translatedValues = array();
                if(is_array($values)) {
                    foreach($values as $value) {
                        $translatedValues[] = $this->getSelectValue($labeledValue->def, $value);
                    }

                    $value = "<div class='optionvalue'>" . implode("</div><div class='optionvalue'>", $translatedValues) . "</div>";
                } else {
                    $value = '';
                }


            } else if($labeledValue->def instanceof ManyToManyObjectRelation) {

                $names = array();
                if(is_array($labeledValue->value)) {
                    foreach($labeledValue->value as $entry) {
                        if($entry instanceof AbstractObject && method_exists($entry, "getName")) {
                            $names[] = $entry->getName();
                        }
                    }
                }

                $value = implode(", ", $names);

            } else if($labeledValue->value instanceof AbstractObject && method_exists($labeledValue->value, "getName")) {
                $value = $labeledValue->value->getName();
            } else if($labeledValue->def instanceof Checkbox) {
                $value = $this->translator->trans("optionvalue." . ($labeledValue->value ? "true" : "false"));
            } else if($labeledValue->def instanceof Image) {
                $value = '<img src="' . $labeledValue->value . '" />';
            } else {
                $value = $labeledValue->value;
                if(is_object($value)) {
                    p_r($labeledValue);
                    p_r($property);
                    die();
                }
            }

            if($labeledValue->value) {
                $result = "
                            <tr>
                                <td class=\"firstcol\">" . $this->translator->trans("attr." . $labeledValue->label) . "</td>
                                <td class=\"secondcol\">" . $value . "</td>
                            </tr>
                ";
                return $result;
            }

        } else {
            p_r($property);
        }

    }


    private function getSelectValue($def, $value) {
        return $this->translator->trans("optionvalue." . $value);
    }
}
