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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.OutputDataConfigDialog");
pimcore.bundle.outputDataConfigToolkit.OutputDataConfigDialog = Class.create(pimcore.object.helpers.gridConfigDialog, {

    data: {},
    brickKeys: [],
    availableOperators: null,

    initialize: function (outputConfig, callback, availableOperators, targetObjectId) {

        if(pimcore.settings === undefined) {
            pimcore.settings = { debug_admin_translations: false };
        }

        this.targetObjectId = targetObjectId;
        this.outputConfig = outputConfig;
        this.callback = callback;
        if(availableOperators) {
            this.availableOperators = availableOperators;
        }

        if(!this.callback) {
            this.callback = function () {};
        }

        this.configPanel = new Ext.Panel({
            layout: "border",
            items: [this.getSelectionPanel(), this.getLeftPanel()]

        });

        this.window = new Ext.Window({
            width: 850,
            height: 650,
            modal: true,
            iconCls: "bundle_outputdataconfig_icon",
            title: t('output_channel_definition_for') + " " + t(this.outputConfig.channel),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
    },


    commitData: function () {
        var data = this.getData();
        this.callback(data);
        this.window.close();
    },

    getData: function () {
        var config = this.doGetRecursiveData(this.selectionPanel.getRootNode());

        this.data = {
            id: this.outputConfig.id,
            config: config
        };
        return this.data;
    },

    doGetRecursiveData: function(node) {
        var childs = [];
        node.eachChild(function(child) {
            var attributes = child.data.configAttributes;
            attributes.childs = this.doGetRecursiveData(child);
            childs.push(attributes);
        }.bind(this));

        return childs;
    },

    expandChildren: function(rootNode) {
        for(var i = 0; i < rootNode.childNodes.length; i++) {
            var child = rootNode.childNodes[i];
            if(child.data.expanded) {
                child.expand();

                if(child.childNodes && child.childNodes.length) {
                    this.expandChildren(child);
                }
            }
        }
    },

    getSelectionPanel: function () {
        if(!this.selectionPanel) {
            var childs = this.doBuildChannelConfigTree(this.outputConfig.configuration);

            var filterField = new Ext.form.field.Text(
                {
                    width: 300,
                    hideLabel: true,
                    enableKeyEvents: true
                }
            );

            var filterButton = new Ext.button.Button({
                iconCls: "pimcore_icon_search"
            });

            var headerConfig = {
                title: t('class_attributes'),
                items: [
                    filterField,
                    filterButton
                ]
            };


            this.selectionPanel = new Ext.tree.TreePanel({
                root: {
                    id: "0",
                    root: true,
                    text: t("output_channel_definition"),
                    leaf: false,
                    isTarget: true,
                    expanded: true,
                    children: childs
                },
                region:'east',
                tbar: headerConfig,

                title: t('output_channel_definition'),
                layout:'fit',
                width: 428,
                split:true,
                autoScroll:true,
                rootVisible: false,
                viewConfig: {
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        ddGroup: "columnconfigelement",
                        allowContainerDrops: true
                    },
                    listeners: {
                        options: {
                            target: this.selectionPanel
                        },
                        beforedrop: function (node, data, overModel, dropPosition, dropHandlers, eOpts) {
                            var target = overModel.getOwnerTree().getView();
                            var source = data.view;

                            if (source != target) {
                                var record = data.records[0];

                                var attr = record.data;
                                if (record.data.configAttributes) {
                                    attr = record.data.configAttributes;
                                }
                                var element = this.getConfigElement(attr);
                                var copy = element.getCopyNode(record);
                                data.records = [copy]; // assign the copy as the new dropNode
                                var window = element.getConfigDialog(copy);

                                if(window) {
                                    //this is needed because of new focus management of extjs6
                                    setTimeout(function() {
                                        window.focus();
                                    }, 250);
                                }
                            }
                        }.bind(this),
                        drop: function(node, data, overModel) {
                            overModel.set('expandable', true);

                        }.bind(this),
                        nodedragover: function (targetNode, position, dragData, e, eOpts ) {
                            var sourceNode = dragData.records[0];

                            var sourceType = this.getNodeTypeAndClass(sourceNode);
                            var targetType = this.getNodeTypeAndClass(targetNode);
                            var allowed = false;

                            //check allowed Parents
                            if (sourceNode.data.allowedParents) {
                                if (position == "append" && sourceNode.data.allowedParents[targetType.type] && sourceNode.data.allowedParents[targetType.type][targetType.className] == true) {
                                    allowed = true;
                                }
                            }

                            //check allowed Types
                            if (targetNode.data.allowedTypes) {
                                if (position == "append" && targetNode.data.allowedTypes[sourceType.type] && targetNode.data.allowedTypes[sourceType.type][sourceType.className] == true) {
                                    allowed = true;
                                }
                            }

                            //if nothing is set --> true
                            if (!sourceNode.data.allowedParents && !targetNode.data.allowedTypes) {
                                allowed = true;
                            }

                            //check count
                            if (targetNode.data.maxChildCount && targetNode.childNodes.length >= targetNode.data.maxChildCount && position == 'append') {
                                allowed = false;
                            }
                            if (targetNode.parentNode && targetNode.parentNode.data.maxChildCount && targetNode.parentNode.childNodes.length >= targetNode.parentNode.data.maxChildCount) {
                                allowed = false;
                            }

                            return allowed;


                        }.bind(this)
                    }
                },
                listeners: {

                    afterrender: function (tree) {

                        //initialise search filter
                        var classTreeHelper = new pimcore.object.helpers.classTree(true);
                        classTreeHelper.updateFilter(tree, filterField);

                        filterField.on("keyup", classTreeHelper.updateFilter.bind(tree, tree, filterField));
                        filterButton.on("click", classTreeHelper.updateFilter.bind(tree, tree, filterField));

                    },
                    afterlayout: function (tree) {
                        this.expandChildren(tree.getRootNode());
                    }.bind(this),
                    itemcontextmenu: function (tree, record, item, index, e, eOpts) {
                        e.stopEvent();

                        tree.select();

                        var menu = new Ext.menu.Menu();

                        if (this.id != 0) {
                            menu.add(new Ext.menu.Item({
                                text: t('delete'),
                                iconCls: "pimcore_icon_delete",
                                handler: function (node) {
                                    if(node.parentNode.childNodes.length == 1) {
                                        node.parentNode.set('expandable', false);
                                    }
                                    node.parentNode.removeChild(node, true);
                                }.bind(this, record)
                            }));
                            menu.add(new Ext.menu.Item({
                                text: t('edit'),
                                iconCls: "pimcore_icon_edit",
                                handler: function (node) {
                                    this.getConfigElement(node.data.configAttributes).getConfigDialog(node);
                                }.bind(this, record)
                            }));
                        }

                        menu.showAt(e.pageX, e.pageY);
                    }.bind(this)
                },
                buttons: [{
                    text: t("apply"),
                    iconCls: "pimcore_icon_apply",
                    handler: function () {
                        this.commitData();
                    }.bind(this)
                }]
            });

        }

        return this.selectionPanel;
    },

    doBuildChannelConfigTree: function(configuration) {
        var elements = [];
        if(configuration) {
            for(var i = 0; i < configuration.length; i++) {
                var treenode = this.getConfigElement(configuration[i]).getConfigTreeNode(configuration[i]);

                if(configuration[i].childs) {
                    var childs = this.doBuildChannelConfigTree(configuration[i].childs);
                    treenode.children = childs;
                    if(childs.length > 0) {
                        treenode.expandable = true;
                    }
                }
                if (configuration[i].icon != null && configuration[i].icon.length > 0) {
                    treenode.iconCls = null;
                    treenode.icon = configuration[i].icon;
                }

                elements.push(treenode);
            }
        }
        return elements;
    },

    getConfigElement: function(configAttributes) {
        var element = null;
        if(configAttributes && configAttributes.class && configAttributes.type) {
            element = new pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements[configAttributes.type][configAttributes.class](this.outputConfig.classId);
        } else {
            var dataType = configAttributes.dataType.charAt(0).toUpperCase() + configAttributes.dataType.slice(1);
            if(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.value[dataType]) {
                element = new pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.value[dataType](this.outputConfig.classId);
            } else {
                element = new pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.value.DefaultValue(this.outputConfig.classId);
            }
        }
        return element;
    },

    getNodeTypeAndClass: function(node) {
        var type = "value";
        var className = "";
        if(node.data.configAttributes) {
            type = node.data.configAttributes.type;
            className = node.data.configAttributes['class'];
        } else if(node.data.dataType) {
            className = node.data.dataType.charAt(0).toUpperCase() + node.data.dataType.slice(1);
        }
        return {type: type, className: className};
    },

    getLeftPanel: function () {
        if (!this.leftPanel) {

            var items = [
                this.getClassTree("/admin/outputdataconfig/get-class-definition-for-column-config", this.outputConfig.classId, this.targetObjectId),
                this.getOperatorTree()
            ];

            this.brickKeys = [];
            this.leftPanel = new Ext.Panel({
                layout: "border",
                region: "center",
                items: items
            });
        }

        return this.leftPanel;
    },

    getClassTree: function(url, id,  target_oid) {

        var classTreeHelper = new pimcore.bundle.outputDataConfigToolkit.ClassTree(false);
        var tree = classTreeHelper.getClassTree(url, id, null, target_oid);

        tree.addListener("itemdblclick", function( tree, record, item, index, e, eOpts ) {

            if(!record.data.root && record.data.type != "layout" && record.data.dataType != 'localizedfields') {
                var attr = record.data;
                if(record.data.configAttributes) {
                    attr = record.data.configAttributes;
                }
                var element = this.getConfigElement(attr);
                var copy = element.getCopyNode(record);
                element.getConfigDialog(copy);

                if(this.selectionPanel) {
                    this.selectionPanel.getRootNode().appendChild(copy);
                }
            }
        }.bind(this));

        tree.addListener("itemcontextmenu", function (tree, record, item, index, e) {
            if (record.data.depth === 1 && record.data.nodeType == "classificationstore") {
                var menu = new Ext.menu.Menu({
                    items: [{
                        text: t("add_all"),
                        iconCls: "pimcore_icon_add",
                        handler: function (item, e) {
                            Ext.Array.forEach(record.childNodes, function (record) {
                                var attr = record.data;
                                if(record.data.configAttributes) {
                                    attr = record.data.configAttributes;
                                }
                                var element = this.getConfigElement(attr);
                                var copy = element.getCopyNode(record);
                                copy.data.configAttributes.icon = null;
                                this.selectionPanel.getRootNode().appendChild(copy);
                            }, this);
                        }.bind(this),
                    }]
                });
                menu.showAt(e.getXY());
                e.stopEvent();
            }

        }.bind(this));

        return tree;
    },

    getOperatorTree: function() {
        var operators = Object.keys(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator);
        var childs = [];
        for(var i = 0; i < operators.length; i++) {
            if(!this.availableOperators || this.availableOperators.indexOf(operators[i]) >= 0) {
                childs.push(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator[operators[i]].prototype.getConfigTreeNode());
            }
        }

        var tree = new Ext.tree.TreePanel({
            title: t('operators'),
            xtype: "treepanel",
            region: "south",
            autoScroll: true,
            height: 200,
            rootVisible: false,
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    ddGroup: "columnconfigelement",
                    allowDrop: false,
                    allowDrag: true
                }
            },
            root: {
                id: "0",
                root: true,
                text: t("base"),
                draggable: false,
                leaf: false,
                isTarget: false,
                children: childs
            }
        });

        return tree;
    }

});
