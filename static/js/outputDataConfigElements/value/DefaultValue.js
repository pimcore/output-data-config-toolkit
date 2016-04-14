/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.value.DefaultValue");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.value.DefaultValue = Class.create(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.Abstract, {

    type: "value",
    class: "DefaultValue",

    getConfigTreeNode: function(configAttributes) {
        var node = {
            draggable: true,
            iconCls: "pimcore_icon_" + configAttributes.dataType,
            text: configAttributes.text,
            qtip: configAttributes.attribute,
            configAttributes: configAttributes,
            isTarget: true,
            leaf: true
        };

        return node;
    },

    getCopyNode: function(source) {
        var copy = new Ext.tree.TreeNode({
            iconCls: source.attributes.iconCls,
            text: source.attributes.text,
            isTarget: true,
            leaf: true,
            dataType: source.attributes.dataType,
            qtip: source.attributes.key,
            configAttributes: {
                label: null,
                type: this.type,
                class: this.class,
                attribute: source.attributes.key,
                dataType: source.attributes.dataType
            }
        });
        return copy;
    },

    getConfigDialog: function(node) {
        this.node = node;

        var value = "original";
        if(this.node.attributes.configAttributes.label) {
            value = "custom";
        }

        this.radiogroup = new Ext.form.RadioGroup({
            fieldLabel: t('config_title'),
            vertical: false,
            columns: 1,
            value: value,
            items: [
                {boxLabel: t('config_title_original'), name: 'rb', inputValue: "original", checked: true},
                {
                    boxLabel: t('config_title_custom'),
                    name: 'rb',
                    inputValue: "custom",
                    listeners: {
                        check: function(element, checked) {
                            this.textfield.setDisabled(!checked);
                        }.bind(this)
                    }
                }
            ]
        });

        this.textfield = new Ext.form.TextField({
            fieldLabel: t('custom_title'),
            disabled: true,
            length: 255,
            width: 200,
            value: this.node.attributes.text
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.radiogroup, this.textfield],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData();
                }.bind(this)
            }]
        });

        this.window = new Ext.Window({
            width: 400,
            height: 200,
            modal: true,
            title: t('attribute_settings'),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
    },

    commitData: function() {
        if(this.radiogroup.getValue().getGroupValue() == "custom") {
            this.node.attributes.configAttributes.label = this.textfield.getValue();
            this.node.setText( this.textfield.getValue() );
        }
        this.window.close();
    }
});