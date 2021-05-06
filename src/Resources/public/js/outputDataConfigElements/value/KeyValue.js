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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.value.KeyValue");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.value.KeyValue = Class.create(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.Abstract, {

    type: "value",
    class: "KeyValue",

    getConfigTreeNode: function(configAttributes) {
        var node = {
            draggable: true,
            iconCls: "pimcore_icon_" + configAttributes.dataType,
            text: this.getText(configAttributes, t("keyValue")),
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

        var data = {records: []};
        if(this.node.data.configAttributes && this.node.data.configAttributes.records) {
            data = this.node.data.configAttributes;
        }

        this.store = new Ext.data.JsonStore({
            data: data.records,
            autoDestroy: true,
            idProperty: 'id',
            fields: [
                "id",
                "group",
                "groupdescription",
                "name",
                "description",
                "label"
            ]
        });

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
        });

        this.grid = Ext.create('Ext.grid.Panel', {
            store: this.store,
            plugins: [
                this.cellEditing
            ],
            columns: [
                {header: 'ID', dataIndex: 'id', width: 50},
                {id: "group", header: t("keyvalue_tag_col_group"), dataIndex: 'group', width: 150, sortable: false},
                {id: "groupdescription", header: t("keyvalue_tag_col_group_description"), dataIndex: 'groupdescription', width: 150, sortable: false},
                {id: "name", header: t("name"), dataIndex: 'name', width: 150, sortable: false},
                {id: "description", header: t("description"), dataIndex: 'description', width: 150, sortable: false},
                {id: "label", header: t("label"), dataIndex: 'label', flex: 150, sortable: false, editable: true, editor: new Ext.form.TextField()},
                {
                    xtype:'actioncolumn',
                    width:30,
                    items:[
                        {
                            tooltip:t('up'),
                            icon:"/bundles/pimcoreadmin/img/flat-color-icons/up.svg",
                            handler:function (grid, rowIndex) {
                                if (rowIndex > 0) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    grid.getStore().removeAt(rowIndex);
                                    grid.getStore().insert(rowIndex - 1, [rec]);
                                }
                            }.bind(this)
                        }
                    ]
                },
                {
                    xtype:'actioncolumn',
                    width:30,
                    items:[
                        {
                            tooltip:t('down'),
                            icon:"/bundles/pimcoreadmin/img/flat-color-icons/down.svg",
                            handler:function (grid, rowIndex) {
                                if (rowIndex < (grid.getStore().getCount() - 1)) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    grid.getStore().removeAt(rowIndex);
                                    grid.getStore().insert(rowIndex + 1, [rec]);
                                }
                            }.bind(this)
                        }
                    ]
                },
                {
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [
                        {
                            tooltip: t('remove'),
                            icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                            handler: function (grid, rowIndex) {
                                grid.getStore().removeAt(rowIndex);
                            }.bind(this)
                        }
                    ]
                }
            ],
            cls: 'object_field',
            tbar: {
                items: [
                    {
                        xtype: "tbtext",
                        text: "<b>" + t("keyvalue_selected_keys") + "</b>"
                    },
                    "->",
                    {
                        xtype: "button",
                        iconCls: "pimcore_icon_delete",
                        handler: function() {
                            this.store.removeAll();
                        }.bind(this)
                    },
                    {
                        xtype: "button",
                        iconCls: "pimcore_icon_add",
                        handler: function() {
                            var selectionWindow = new pimcore.object.keyvalue.selectionwindow(this);
                            selectionWindow.show();
                        }.bind(this)
                    }
                ],
                ctCls: "pimcore_force_auto_width",
                cls: "pimcore_force_auto_width"
            },
            autoHeight: true,
            bodyCls: "pimcore_editable_grid"
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.grid],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData();
                }.bind(this)
            }]
        });

        this.window = new Ext.Window({
            width: 1000,
            height: 400,
            modal: true,
            autoScroll: true,
            title: t('attribute_settings'),
            items: [this.configPanel]
        });

        this.window.show();
        return this.window;
    },


    handleSelectionWindowClosed: function() {
        // nothing to do
    },

    requestPending: function() {
        // nothing to do
    },

    handleAddKeys: function (response) {
        var data = Ext.decode(response.responseText);

        if(data && data.success) {
            for (var i=0; i < data.data.length; i++) {
                var keyDef = data.data[i];

                var totalCount = this.store.data.length;

                var addKey = true;
                for (var x = 0; x < totalCount; x++) {
                    var record = this.store.getAt(x);

                    if (record.data.key == keyDef.id) {
                        addKey = false;
                        break;
                    }
                }

                if (addKey) {
                    var colData = {};
                    colData.id = keyDef.id;
                    colData.name = keyDef.name;
                    colData.description = keyDef.description;
                    colData.group = keyDef.groupName;
                    colData.groupdescription = keyDef.groupdescription;
                    this.store.add(colData);
                }
            }
        }

    },

    commitData: function() {

        var records = [];

        this.store.each(function(record) {
            records.push(record.data);
        });

        this.node.data.configAttributes.records = records;
        this.node.set('text', this.getText(this.node.data.configAttributes, this.node.text) );

        this.window.close();
    },

    getText: function(config, text) {
        if(!text) {
            text = config.text;
        }
        if(config.records.length) {
            var fields = "";
            for(var i = 0; i < config.records.length; i++) {
                fields += config.records[i].name + ", ";
            }

            return text + " (" + fields + ")";
        }

    }
});