pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.value.KeyValue");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.value.KeyValue = Class.create(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.Abstract, {

    type: "value",
    class: "KeyValue",

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
        var copy = new Ext.tree.TreeNode({
            iconCls: source.attributes.iconCls,
            text: source.attributes.text,
            isTarget: true,
            leaf: true,
            dataType: source.attributes.dataType,
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

        var data = {records: []};
        if(this.node.attributes.configAttributes && this.node.attributes.configAttributes.records) {
            data = this.node.attributes.configAttributes;
        }

        this.store = new Ext.data.JsonStore({
            data: data,
            root: 'records',
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

        this.grid = new Ext.grid.EditorGridPanel({
            store: this.store,
            sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
            colModel: new Ext.grid.ColumnModel({
                defaults: {
                    sortable: false
                },
                columns: [
                    {header: 'ID', dataIndex: 'id', width: 50},
                    {id: "group", header: t("keyvalue_tag_col_group"), dataIndex: 'group', width: 150},
                    {id: "groupdescription", header: t("keyvalue_tag_col_group_description"), dataIndex: 'groupdescription', width: 150},
                    {id: "name", header: t("name"), dataIndex: 'name', width: 150},
                    {id: "description", header: t("description"), dataIndex: 'description', width: 150},
                    {id: "label", header: t("label"), dataIndex: 'label', width: 150, editor: new Ext.form.TextField()},
                    {
                        xtype:'actioncolumn',
                        width:30,
                        items:[
                            {
                                tooltip:t('up'),
                                icon:"/pimcore/static/img/icon/arrow_up.png",
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
                                icon:"/pimcore/static/img/icon/arrow_down.png",
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
                                icon: "/pimcore/static/img/icon/cross.png",
                                handler: function (grid, rowIndex) {
                                    grid.getStore().removeAt(rowIndex);
                                }.bind(this)
                            }
                        ]
                    }
                ]
            }),
            cls: 'object_field',
            autoExpandColumn: 'label',
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
            bodyCssClass: "pimcore_object_tag_objects"
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
                    this.store.add(new this.store.recordType(colData));
                }
            }
        }

    },

    commitData: function() {

        var records = [];

        this.store.each(function(record) {
            records.push(record.data);
        });

        this.node.attributes.configAttributes.records = records;
        this.node.setText( this.getText(this.node.attributes.configAttributes, this.node.text) );

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