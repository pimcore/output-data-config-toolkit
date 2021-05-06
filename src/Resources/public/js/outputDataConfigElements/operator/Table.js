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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Table");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Table = Class.create(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Group, {
    type: "operator",
    class: "Table",
    iconCls: "pimcore_icon_operator_table",
    defaultText: "operator_table",
    windowTitle: "table_operator_settings",
    allowedTypes: {
        operator: { 
            TableRow: true
        }
    },

    getConfigDialog: function(node) {
        this.node = node;

        this.textfield = new Ext.form.TextField({
            fieldLabel: t('text'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.label
        });

        this.tooltip = new Ext.form.TextArea({
            fieldLabel: t('tooltip'),
            length: 255,
            width: 200,
            height: 100,
            value: this.node.data.configAttributes.tooltip
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.textfield, this.tooltip],
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
            height: 250,
            modal: true,
            title: t(this.windowTitle),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
        return this.window;
    },

    commitData: function() {
        this.node.data.configAttributes.label = this.textfield.getValue();
        this.node.data.configAttributes.tooltip = this.tooltip.getValue();
        this.node.set('text', this.textfield.getValue());
        this.window.close();
    }


});