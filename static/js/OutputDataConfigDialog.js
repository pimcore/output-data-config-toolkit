

pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.OutputDataConfigDialog");
pimcore.plugin.outputDataConfigToolkit.OutputDataConfigDialog = Class.create(pimcore.object.helpers.gridConfigDialog, {

    data: {},
    brickKeys: [],
    availableOperators: null,


    initialize: function (outputConfig, callback, availableOperators) {

        if(pimcore.settings === undefined) {
            pimcore.settings = { debug_admin_translations: false };
        }

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
            iconCls: "plugin_outputdataconfig_icon",
            title: t('output_channel_definition_for') + " " + ts(this.outputConfig.channel),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
        this.selectionPanel.getRootNode().eachChild(function(child) {
            child.collapse(true, false);
        }.bind(this));
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
            var attributes = child.attributes.configAttributes;
            attributes.childs = this.doGetRecursiveData(child);
            childs.push(attributes);
        }.bind(this));

        return childs;
    },

    expandChildren: function(rootNode) {

        for(var i = 0; i < rootNode.childNodes.length; i++) {
            var child = rootNode.childNodes[i];

            if(child.attributes.expanded) {
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
            this.selectionPanel = new Ext.tree.TreePanel({
                root: {
                    id: "0",
                    root: true,
                    text: t("output_channel_definition"),
                    reference: this,
                    leaf: false,
                    isTarget: true,
                    expanded: true,
                    children: childs
                },
                dropConfig: {ddGroup: "columnconfigelement", appendOnly:false, allowContainerDrop: true},
                enableDD:true,
                ddGroup: "columnconfigelement",
                id:'tree',
                region:'east',
                title: t('output_channel_definition'),
                layout:'fit',
                width: 428,
                split:true,
                autoScroll:true,
                rootVisible: false,
                listeners:{
                    afterlayout: function(tree) {
                        this.expandChildren(tree.getRootNode());
                    }.bind(this),
                    beforenodedrop: function(e) {
                        if(e.source.tree.el != e.target.ownerTree.el) {
                            var n = e.dropNode; // the node that was dropped
                            var attr = n.attributes;
                            if(n.attributes.configAttributes) {
                                attr = n.attributes.configAttributes;
                            }
                            var element = this.getConfigElement(attr);
                            var copy = element.getCopyNode(n);
                            e.dropNode = copy; // assign the copy as the new dropNode
                            element.getConfigDialog(copy);
                        }
                    }.bind(this),
                    nodedragover: function(dragOverEvent) {
                        var sourceNode = dragOverEvent.dropNode;
                        var targetNode = dragOverEvent.target;

                        var sourceType = this.getNodeTypeAndClass(sourceNode);
                        var targetType = this.getNodeTypeAndClass(targetNode);

                        var allowed = false;

                        //check allowed Parents
                        if(sourceNode.attributes.allowedParents) {
                            if(dragOverEvent.point == "append" && sourceNode.attributes.allowedParents[targetType.type] && sourceNode.attributes.allowedParents[targetType.type][targetType.className] == true) {
                                allowed = true;
                            }
                        }

                        //check allowed Types
                        if(targetNode.attributes.allowedTypes) {
                            if(dragOverEvent.point == "append" && targetNode.attributes.allowedTypes[sourceType.type] && targetNode.attributes.allowedTypes[sourceType.type][sourceType.className] == true) {
                                allowed = true;
                            }
                        }

                        //if nothing is set --> true
                        if(!sourceNode.attributes.allowedParents && !targetNode.attributes.allowedTypes) {
                            allowed = true;
                        }

                        //check count
                        if(targetNode.attributes.maxChildCount && targetNode.childNodes.length >= targetNode.attributes.maxChildCount && dragOverEvent.point == 'append') {
                            allowed = false;
                        }
                        if(targetNode.parentNode && targetNode.parentNode.attributes.maxChildCount && targetNode.parentNode.childNodes.length >= targetNode.parentNode.attributes.maxChildCount) {
                            allowed = false;
                        }

                        dragOverEvent.cancel = !allowed;


                    }.bind(this),
                    contextmenu: function(node) {
                        node.select();

                        var menu = new Ext.menu.Menu();

                        if (this.id != 0) {
                            menu.add(new Ext.menu.Item({
                                text: t('delete'),
                                iconCls: "pimcore_icon_delete",
                                handler: function(node) {
                                    node.parentNode.removeChild(node, true);
                                }.bind(this, node)
                            }));
                            menu.add(new Ext.menu.Item({
                                text: t('edit'),
                                iconCls: "pimcore_icon_edit",
                                handler: function(node) {
                                    this.getConfigElement(node.attributes.configAttributes).getConfigDialog(node);
                                }.bind(this, node)
                            }));
                        }

                        menu.show(node.ui.getEl());
                    }.bind(this),
                    dblclick: function(node) {
                        this.getConfigElement(node.attributes.configAttributes).getConfigDialog(node);
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
                }
                elements.push(treenode);
            }
        }
        return elements;
    },

    getConfigElement: function(configAttributes) {
        var element = null;
        if(configAttributes && configAttributes.class && configAttributes.type) {
            element = new pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements[configAttributes.type][configAttributes.class](this.outputConfig.o_classId);
        } else {
            var dataType = configAttributes.dataType.charAt(0).toUpperCase() + configAttributes.dataType.slice(1);
            if(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.value[dataType]) {
                element = new pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.value[dataType](this.outputConfig.o_classId);
            } else {
                element = new pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.value.DefaultValue(this.outputConfig.o_classId);
            }
        }
        return element;
    },

    getNodeTypeAndClass: function(node) {
        var type = "value";
        var className = "";
        if(node.attributes.configAttributes) {
            type = node.attributes.configAttributes.type;
            className = node.attributes.configAttributes['class'];
        } else if(node.attributes.dataType) {
            className = node.attributes.dataType.charAt(0).toUpperCase() + node.attributes.dataType.slice(1);
        }
        return {type: type, className: className};
    },

    getLeftPanel: function () {
        if (!this.leftPanel) {

            var items = [
                this.getClassTree("/admin/class/get-class-definition-for-column-config", this.outputConfig.o_classId),
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

    getClassTree: function(url, id) {

        var classTreeHelper = new pimcore.object.helpers.classTree(false);
        var tree = classTreeHelper.getClassTree(url, id);

        tree.addListener("dblclick", function(node) {
            if(!node.attributes.root && node.attributes.type != "layout" && node.attributes.dataType != 'localizedfields') {
                var attr = node.attributes;
                if(node.attributes.configAttributes) {
                    attr = node.attributes.configAttributes;
                }
                var element = this.getConfigElement(attr);
                var copy = element.getCopyNode(node);
                element.getConfigDialog(copy);

                if(this.selectionPanel) {
                    this.selectionPanel.getRootNode().appendChild(copy);
                }
            }
        }.bind(this));

        return tree;
    },

    getOperatorTree: function() {
        var operators = Object.keys(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator);
        var childs = [];
        for(var i = 0; i < operators.length; i++) {
            if(!this.availableOperators || this.availableOperators.indexOf(operators[i]) >= 0) {
            childs.push(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator[operators[i]].prototype.getConfigTreeNode());
        }
        }

        var tree = new Ext.tree.TreePanel({
            title: t('operators'),
            xtype: "treepanel",
            region: "south",
            enableDrag: true,
            enableDrop: false,
            ddGroup: "columnconfigelement",
            autoScroll: true,
            height: 200,
            rootVisible: false,
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
