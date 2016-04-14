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


pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.Tab");

pimcore.plugin.outputDataConfigToolkit.Tab = Class.create({

    initialize: function(object, type) {
        this.object = object;
        this.type = type;
    },

    load: function () {
    },

    getLayout: function () {


        if (this.layout == null) {
            var classStore = pimcore.globalmanager.get("object_types_store");
            var toolbarConfig = [new Ext.Toolbar.TextItem({
                text: t("please_select_a_type")
            }),new Ext.form.ComboBox({
                name: "selectClass",
                //listWidth: 'auto',
                store: classStore,
                valueField: 'id',
                displayField: 'translatedText',
                triggerAction: 'all',
                listeners: {
                    "select": function(field, newValue, oldValue) {
                        this.store.load({params: {"class_id": newValue.data.id}});
                    }.bind(this)
                }
            })];


            this.layout = new Ext.Panel({
                title: t('outputdataconfig'),
                border: false,
                layout: "fit",
                iconCls: "plugin_outputdataconfig_icon",
                tbar: toolbarConfig,
                items: [this.getGrid()]
            });
        }
        return this.layout;

    },

    getGrid: function() {
        var proxy = new Ext.data.HttpProxy({
            url: '/plugin/Elements_OutputDataConfigToolkit/admin/get-output-configs'
        });
        var readerFields = [
            {name: 'id'},
            {name: 'classname'},
            {name: 'object_id'},
            {name: 'channel'},
            {name: 'is_inherited'}
        ];
        var reader = new Ext.data.JsonReader({
            totalProperty: 'total',
            successProperty: 'success',
            root: 'data',
            idProperty: 'id'
        }, readerFields);

        var writer = new Ext.data.JsonWriter();

        this.store = new Ext.data.GroupingStore({
            restful: false,
            proxy: proxy,
            reader: reader,
            writer: writer,
            groupField: "channel",
            listeners: {
                write : function(store, action, result, response, rs) {
                }
            },
            baseParams:{object_id: this.object.id}
        });
        //this.store.load();

        var columns = [
            {header: t('channel'), width: 30, dataIndex: 'channel'},
            {header: t('class'), width: 20, dataIndex: 'classname'},
            {header: t('objectid'), width: 10, dataIndex: 'object_id', renderer: function (value, metaData, record) {
                if(record.data.is_inherited == true) {
                    metaData.css += " grid_value_inherited";
                }
                return value;}
            }
        ];

        columns.push({
            header: t('overwrite/edit'),
            xtype: 'actioncolumn',
            width: 20,
            items: [
                {
                    tooltip: t('overwrite/edit'),
                    icon: "/pimcore/static/img/icon/pencil_go.png",
                    handler: function (grid, rowIndex) {
                        var data = grid.getStore().getAt(rowIndex);
                        this.openConfigDialog(data.data.id);
                    }.bind(this)
                }
            ]
        });
        columns.push({
            header: t('reset'),
            xtype: 'actioncolumn',
            width: 20,
            items: [
                {
                    tooltip: t('reset'),
                    icon: "/pimcore/static/img/icon/cross.png",
                    getClass: function(v, meta, rec) {  // Or return a class from a function
                        if(rec.get('is_inherited') || this.object.id == 1) {
                            return "pimcore_hidden";
                        }
                    }.bind(this),
                    handler: function (grid, rowIndex) {
                        var data = grid.getStore().getAt(rowIndex);
                        Ext.MessageBox.confirm(t('reset_outputdataconfig'), t('reset_outputdataconfig_text'), this.resetOutputDataConfig.bind(this, data.data.id), this);
                    }.bind(this)
                }
            ]
        });


        this.configGrid = new Ext.grid.GridPanel({
            store: this.store,
            autoScroll: true,
            cm: new Ext.grid.ColumnModel({
                defaults: {
                    width: 20,
                    sortable: true
                },
                columns: columns
            }),
            view: new Ext.grid.GroupingView({
                forceFit: true
            }),
            width: 600,
            height: 300,
            collapsible: true,
            animCollapse: false,
            tbar: [
                {
                    text: t('reload'),
                    handler: function () {
                        this.store.reload();
                    }.bind(this),
                    iconCls: "pimcore_icon_reload"
                }
            ]
        });

        return this.configGrid;
    },

    resetOutputDataConfig: function(configId, answer) {
        if(answer != "no") {
            Ext.Ajax.request({
                url: '/plugin/Elements_OutputDataConfigToolkit/admin/reset-output-config',
                params: {
                    config_id: configId
                },
                success: function(response) {
                    var data = Ext.decode(response.responseText);

                    if(data.success) {
                        this.store.reload();
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("error_reseting_output_config"), "error", t(data.message));
                    }
                }.bind(this)
            });
        }
    },

    openConfigDialog: function(configId) {
        Ext.Ajax.request({
            url: '/plugin/Elements_OutputDataConfigToolkit/admin/get-output-config',
            params: {
                config_id: configId
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if(data.success) {
                    var dialog = new pimcore.plugin.outputDataConfigToolkit.OutputDataConfigDialog(data.outputConfig, this.saveConfigDialog.bind(this));
                } else {
                    pimcore.helpers.showNotification(t("error"), t("error_opening_output_config"), "error", t(data.message));
                }
            }.bind(this)
        });

    },

    saveConfigDialog: function(data) {
        Ext.Ajax.request({
            url: '/plugin/Elements_OutputDataConfigToolkit/admin/save-output-config',
            method: 'POST',
            params: {
                config_id: data.id,
                object_id: this.object.id,
                config: Ext.encode(data.config)
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if(data.success) {
                    pimcore.helpers.showNotification(t("success"), t("output_config_saved"), "success");
                    this.store.reload();
                } else {
                    pimcore.helpers.showNotification(t("error"), t("error_saving_output_config"), "error", t(data.message));
                }
            }.bind(this)
        });

    }

});