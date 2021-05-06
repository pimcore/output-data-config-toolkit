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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.TableCol");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.TableCol = Class.create(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Group, {
    type: "operator",
    class: "TableCol",
    iconCls: "pimcore_icon_operator_tablecol",
    defaultText: "operator_tablecol",
    windowTitle: "tablecol_operator_settings",
    allowedTypes: null,
    allowedParents: {
        operator: {
            TableRow: true
        }
    },
    maxChildCount: 1,


    getConfigTreeNode: function(configAttributes) {
        if(configAttributes) {
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText) + " (" + configAttributes.colspan + ")",
                configAttributes: configAttributes,
                isTarget: true,
                allowChildren: true,
                allowedTypes: this.allowedTypes,
                allowedParents: this.allowedParents,
                maxChildCount: this.maxChildCount,
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
                allowedTypes: this.allowedTypes,
                allowedParents: this.allowedParents,
                maxChildCount: this.maxChildCount,
                isTarget: true,
                leaf: true
            };
        }
        return node;
    },


    getConfigDialog: function(node) {
        this.node = node;

        var value = 1;
        if(this.node.data.configAttributes.colspan) {
            value = this.node.data.configAttributes.colspan;
        }

        this.numberfield = new Ext.form.NumberField({
            fieldLabel: t('colspan'),
            length: 255,
            width: 200,
            value: value
        });

        this.headline = new Ext.form.Checkbox({
            fieldLabel: t('headline'),
            checked: this.node.data.configAttributes.headline
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.numberfield, this.headline],
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
            height: 150,
            modal: true,
            title: t(this.windowTitle),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
        return this.window;
    },

    commitData: function() {
        this.node.data.configAttributes.colspan = this.numberfield.getValue();
        this.node.data.configAttributes.headline = this.headline.getValue();
        this.node.set('text', this.node.data.text + " (" + this.numberfield.getValue() + ")" );
        this.window.close();
    }

});