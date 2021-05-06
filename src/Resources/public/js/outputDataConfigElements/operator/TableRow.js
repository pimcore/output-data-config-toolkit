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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.TableRow");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.TableRow = Class.create(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.Group, {
    type: "operator",
    class: "TableRow",
    iconCls: "pimcore_icon_operator_tablerow",
    defaultText: "operator_tablerow",
    windowTitle: "tablerow_operator_settings",
    allowedTypes: null,
    allowedParents: {
        operator: {
            Table: true
        }
    },

    getConfigDialog: function(node) {
        this.node = node;


        this.headline = new Ext.form.Checkbox({
            fieldLabel: t('headline'),
            checked: this.node.data.configAttributes.headline
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.headline],
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
        this.node.data.configAttributes.headline = this.headline.getValue();
        this.window.close();
    }


});