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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.Tab");

pimcore.bundle.outputDataConfigToolkit.Tab = Class.create({

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
                store: classStore,
                valueField: 'id',
                displayField: 'translatedText',
                triggerAction: 'all',
                listeners: {
                    "select": function(field, newValue, oldValue) {
                        this.store.load({params: {"class_id": newValue.data.id}});
                    }.bind(this)
                }
            }),{
                xtype: 'button',
                text: t('reload'),
                handler: function () {
                    this.store.reload();
                }.bind(this),
                iconCls: "pimcore_icon_reload"
            }];


            this.layout = new Ext.Panel({
                border: false,
                layout: "fit",
                iconCls: "bundle_outputdataconfig_icon_material pimcore_material_icon",
                tbar: toolbarConfig,
                listeners: {
                    afterrender: function(){
                        this.layout.insert(this.layout.items.length, this.getGrid());
                    }.bind(this)
                }
            });
        }
        return this.layout;

    },

    getGrid: function() {

        var itemsPerPage = 100000000;
        this.store = pimcore.helpers.grid.buildDefaultStore(
            '/admin/outputdataconfig/admin/get-output-configs?',
            [
                {name: 'id'},
                {name: 'classname'},
                {name: 'object_id'},
                {name: 'channel', type: 'string'},
                {name: 'is_inherited'}
            ],
            itemsPerPage
        );
        var proxy = this.store.getProxy();
        proxy.extraParams.object_id = this.object.id;
        this.store.setGroupField('classname');
        this.store.setGroupDir('ASC');

        var columns = [
            {header: t('class'), flex: 20, dataIndex: 'classname', sortable: false},
            {header: t('channel'), flex: 30, dataIndex: 'channel', sortable: false},
            {header: t('objectid'), flex: 10, dataIndex: 'object_id', sortable: false, renderer: function (value, metaData, record) {
                if(record.data.is_inherited == true) {
                    metaData.css += " grid_value_inherited";
                }
                return value;}
            }
        ];

        columns.push({
            header: t('overwrite/edit'),
            xtype: 'actioncolumn',
            width: 60,
            items: [
                {
                    tooltip: t('overwrite/edit'),
                    icon: "/bundles/pimcoreadmin/img/flat-color-icons/edit.svg",
                    handler: function (grid, rowIndex, colIndex, item, e, record, row) {
                        this.openConfigDialog(record.id);
                    }.bind(this)
                }
            ]
        });
        columns.push({
            header: t('reset'),
            xtype: 'actioncolumn',
            width: 60,
            items: [
                {
                    tooltip: t('reset'),
                    icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                    getClass: function(v, meta, rec) {  // Or return a class from a function
                        if(rec.get('is_inherited') || this.object.id == 1) {
                            return "pimcore_hidden";
                        }
                    }.bind(this),
                    handler: function (grid, rowIndex, colIndex, item, event, record) {
                        Ext.MessageBox.confirm(t('reset_outputdataconfig'), t('reset_outputdataconfig_text'), this.resetOutputDataConfig.bind(this, record.data.id), this);
                    }.bind(this)
                }
            ]
        });


        this.configGrid = Ext.create('Ext.grid.Panel', {
            frame: false,
            autoScroll: true,
            store: this.store,
            columns : columns,
            trackMouseOver: true,
            columnLines: true,
            bodyCls: "pimcore_editable_grid",
            selModel: Ext.create('Ext.selection.RowModel', {}),
            stripeRows: true,
            features: [{
                id: 'group',
                ftype: 'grouping',
                groupHeaderTpl: '{name}',
                hideGroupedHeader: true,
                enableGroupingMenu: false,
                enableNoGroups:false,
                startCollapsed: true
            }],
            viewConfig: {
                forceFit: true
            }
        });

        return this.configGrid;
    },

    resetOutputDataConfig: function(configId, answer) {
        if(answer != "no") {
            Ext.Ajax.request({
                url: '/admin/outputdataconfig/admin/reset-output-config',
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
            url: '/admin/outputdataconfig/admin/get-output-config',
            params: {
                config_id: configId
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if(data.success) {
                    var dialog = new pimcore.bundle.outputDataConfigToolkit.OutputDataConfigDialog(data.outputConfig, this.saveConfigDialog.bind(this), null, this.object.id);
                } else {
                    pimcore.helpers.showNotification(t("error"), t("error_opening_output_config"), "error", t(data.message));
                }
            }.bind(this)
        });

    },

    saveConfigDialog: function(data) {
        Ext.Ajax.request({
            url: '/admin/outputdataconfig/admin/save-output-config',
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