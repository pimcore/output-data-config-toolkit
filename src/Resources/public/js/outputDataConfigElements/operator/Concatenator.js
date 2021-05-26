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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Concatenator");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Concatenator = Class.create(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Text, {
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
                leaf: false,
                expandable: false
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
        var copy = source.createNode({
            iconCls: this.iconCls,
            text: source.data.text,
            isTarget: true,
            leaf: false,
            expandable: false,
            configAttributes: {
                label: source.data.text,
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
            value: this.node.data.configAttributes.label
        });

        this.glue = new Ext.form.TextField({
            fieldLabel: t('glue'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.glue
        });
        this.forceValue = new Ext.form.Checkbox({
            fieldLabel: t('force_value'),
            length: 255,
            width: 200,
            checked: this.node.data.configAttributes.forceValue
        });
        this.formatNumbers = new Ext.form.Checkbox({
            fieldLabel: t('format_numbers'),
            length: 255,
            width: 200,
            checked: this.node.data.configAttributes.formatNumbers
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.textfield, this.glue, this.forceValue, this.formatNumbers],
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
            title: t('concatenator_operator_settings'),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
        return this.window;
    },

    commitData: function() {
        this.node.data.configAttributes.label = this.textfield.getValue();
        this.node.set('text', this.textfield.getValue());
        this.node.data.configAttributes.glue = this.glue.getValue();
        this.node.data.configAttributes.forceValue = this.forceValue.getValue();
        this.node.data.configAttributes.formatNumbers = this.formatNumbers.getValue();
        this.window.close();
    }
});