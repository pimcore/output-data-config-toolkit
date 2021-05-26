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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.value.StructuredTable");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.value.StructuredTable = Class.create(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.Abstract, {

    type: "value",
    class: "StructuredTable",

    getConfigTreeNode: function(configAttributes) {
        var node = {
            draggable: true,
            iconCls: "pimcore_icon_" + configAttributes.dataType,
            text: this.getText(configAttributes, configAttributes.text),
            configAttributes: configAttributes,
            isTarget: true,
            leaf: true
        };

        return node;
    },

    getCopyNode: function(source) {
        var copy = source.createNode({
            iconCls: source.data.iconCls,
            text: source.data.text,
            isTarget: true,
            leaf: true,
            dataType: source.data.dataType,
            configAttributes: {
                label: null,
                type: this.type,
                class: this.class,
                attribute: source.data.key,
                dataType: source.data.dataType
            }
        });
        return copy;
    },

    getConfigDialog: function(node) {
        this.node = node;

        Ext.Ajax.request({
            url: '/admin/outputdataconfig/admin/get-field-definition',
            params: {
                class_id: this.objectClassId,
                key: node.data.configAttributes.attribute
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);
                if(data.success) {
                    this.openConfigDialog(data.fieldDefinition);
                } else {
                    pimcore.helpers.showNotification(t("error"), t("error_getting_field_definition"), "error", t(data.message));
                }
            }.bind(this)
        });
    },

    openConfigDialog: function(def) {


        var value = "original";
        if(this.node.data.configAttributes.label) {
            value = "custom";
        }

        this.textfield = new Ext.form.TextField({
            fieldLabel: t('custom_title'),
            disabled: true,
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.label
        });

        this.radiogroup = new Ext.form.RadioGroup({
            fieldLabel: t('config_title'),
            vertical: false,
            columns: 1,
            value: {rb: value},
            items: [
                {boxLabel: t('config_title_original'), name: 'rb', inputValue: "original", checked: true},
                {
                    boxLabel: t('config_title_custom'),
                    name: 'rb',
                    inputValue: "custom",
                    listeners: {
                        change: function(element, newValue) {
                            this.textfield.setDisabled(!newValue);
                        }.bind(this)
                    }
                }
            ]
        });



        var rows = [];
        for(var j = 0; j < def.rows.length; j++) {
            rows.push([def.rows[j].key, def.rows[j].label]);
        }

        var cols = [];
        for(var j = 0; j < def.cols.length; j++) {
            cols.push([def.cols[j].key, def.cols[j].label]);
        }

        this.comboRow = new Ext.form.ComboBox({
            fieldLabel: t('row'),
            disabled: true,
            length: 255,
            width: 200,
            mode: 'local',
            store: new Ext.data.ArrayStore({
                id: 0,
                fields: [
                    'id',
                    'displayText'
                ],
                data: rows
            }),
            valueField: 'id',
            displayField: 'displayText',
            value: this.node.data.configAttributes.row
        });
        this.comboCol = new Ext.form.ComboBox({
            fieldLabel: t('column'),
            length: 255,
            disabled: true,
            width: 200,
            mode: 'local',
            store: new Ext.data.ArrayStore({
                id: 0,
                fields: [
                    'id',
                    'displayText'
                ],
                data: cols
            }),
            valueField: 'id',
            displayField: 'displayText',
            value: this.node.data.configAttributes.col
        });

        var value = "specific_field";
        if(this.node.data.configAttributes.wholeTable) {
            value = "whole_table";
        }
        this.radiogroupTable = new Ext.form.RadioGroup({
            fieldLabel: t('config_table'),
            value: {rbTable: value},
            vertical: false,
            columns: 1,
            items: [
                {boxLabel: t('config_whole_table'), name: 'rbTable', inputValue: "whole_table", checked: true},
                {
                    boxLabel: t('config_specific_field'),
                    name: 'rbTable',
                    inputValue: "specific_field",
                    listeners: {
                        change: function(element, newValue) {
                            this.comboRow.setDisabled(!newValue);
                            this.comboCol.setDisabled(!newValue);
                        }.bind(this)
                    }
                }
            ]
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.radiogroup, this.textfield, this.radiogroupTable, this.comboRow, this.comboCol],
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
            height: 410,
            modal: true,
            title: t('attribute_settings'),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();

        //this is needed because of new focus management of extjs6
        setTimeout(function() {
            this.window.focus();
        }.bind(this), 250);

    },

    commitData: function() {
        if(this.radiogroupTable.getValue().rbTable == "whole_table") {
            this.node.data.configAttributes.wholeTable = true;
            this.node.data.configAttributes.col = null;
            this.node.data.configAttributes.row = null;
        } else {
            this.node.data.configAttributes.wholeTable = false;
            this.node.data.configAttributes.col = this.comboCol.getValue();
            this.node.data.configAttributes.row = this.comboRow.getValue();
        }

        if(this.radiogroup.getValue().rb == "custom") {
            this.node.data.configAttributes.label = this.textfield.getValue();
            this.node.set('text', this.getText(this.node.data.configAttributes, this.textfield.getValue()) );
        } else {
            this.node.data.configAttributes.label = null;
            this.node.set('text', this.getText(this.node.data.configAttributes, this.node.text));
        }

        this.window.close();
    },

    getText: function(config, text) {
        if(!text) {
            text = config.text;
        }
        if(config.wholeTable) {
            return text;
        } else {
            return text + " (" + config.row + ", " + config.col + ")";
        }

    }
});