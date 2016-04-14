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


pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Concatenator");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Concatenator = Class.create(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Text, {
    type: "operator",
    class: "Concatenator",
    iconCls: "pimcore_icon_operator_concatenator",
    defaultText: "operator_concatenator",

    getConfigTreeNode: function(configAttributes) {
        if(configAttributes) {
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: configAttributes.label,
                configAttributes: configAttributes,
                isTarget: true,
                allowChildren: true,
                expanded: true,
                leaf: false
            };
        } else {

            //For building up operator list
            var configAttributes = { type: this.type, class: this.class};

            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText),
                configAttributes: configAttributes,
                isTarget: true,
                leaf: true
            };
        }
        return node;
    },


    getCopyNode: function(source) {
        var copy = new Ext.tree.TreeNode({
            iconCls: this.iconCls,
            text: source.attributes.text,
            isTarget: true,
            leaf: false,
            expanded: true,
            configAttributes: {
                label: source.attributes.text,
                type: this.type,
                class: this.class
            }
        });
        return copy;
    },


    getConfigDialog: function(node) {
        this.node = node;

        this.textfield = new Ext.form.TextField({
            fieldLabel: t('label'),
            length: 255,
            width: 200,
            value: this.node.attributes.configAttributes.label
        });

        this.glue = new Ext.form.TextField({
            fieldLabel: t('glue'),
            length: 255,
            width: 200,
            value: this.node.attributes.configAttributes.glue
        });
        this.forceValue = new Ext.form.Checkbox({
            fieldLabel: t('force_value'),
            length: 255,
            width: 200,
            checked: this.node.attributes.configAttributes.forceValue
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.textfield, this.glue, this.forceValue],
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
            height: 180,
            modal: true,
            title: t('concatenator_operator_settings'),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
    },

    commitData: function() {
        console.log(this.forceValue.getValue());
        this.node.attributes.configAttributes.label = this.textfield.getValue();
        this.node.setText( this.textfield.getValue() );
        this.node.attributes.configAttributes.glue = this.glue.getValue();
        this.node.attributes.configAttributes.forceValue = this.forceValue.getValue();
        this.window.close();
    }
});