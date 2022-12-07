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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.ClassTree");
pimcore.bundle.outputDataConfigToolkit.ClassTree = Class.create(pimcore.object.helpers.classTree, {

    initLayoutFields: function (tree, response) {
        var data = Ext.decode(response.responseText);

        var keys = Object.keys(data);
        for (var i = 0; i < keys.length; i++) {
            if (data[keys[i]]) {
                if (data[keys[i]].children) {
                    var nodeData = data[keys[i]],
                        text = t(nodeData.nodeLabel),
                        brickDescriptor = {},
                        classificationDescriptor = {},
                        nodeType = nodeData.nodeType;

                    if (nodeType == "objectbricks") {
                        brickDescriptor = {
                            insideBrick: true,
                            brickType: nodeData.nodeLabel,
                            brickField: nodeData.brickField
                        };

                        text = t(nodeData.nodeLabel) + " " + t("columns");

                    }

                    var baseNode = {
                        nodeType: nodeType,
                        type: "layout",
                        allowDrag: false,
                        iconCls: "pimcore_icon_" + nodeType,
                        text: text
                    };

                    baseNode = tree.getRootNode().appendChild(baseNode);
                    for (let j = 0; j < data[keys[i]].children.length; j++) {
                        let newChild = data[keys[i]].children[j];

                        if (nodeType === "classificationstore") {
                            classificationDescriptor = {
                                keyConfig: {
                                    id: newChild.id,
                                    name: newChild.name,
                                }
                            };
                            newChild = newChild.definition;
                        }

                        baseNode.appendChild(this.recursiveAddNode(newChild, baseNode, brickDescriptor, classificationDescriptor));
                    }
                    if (nodeType == "object") {
                        baseNode.expand();
                    } else {
                        // baseNode.collapse();
                    }
                }
            }
        }
    },

    recursiveAddNode: function (con, scope, brickDescriptor, classificationDescriptor) {

        var fn = null;
        var newNode = null;

        if (con.datatype == "layout" || con.fieldtype == "classificationstore") {
            fn = this.addLayoutChild.bind(scope, con.fieldtype, con, this);
        } else if (con.datatype == "data") {
            fn = this.addDataChild.bind(scope, con.fieldtype, con, this.showFieldName, brickDescriptor, classificationDescriptor);
        }

        newNode = fn();

        if (con.children) {
            for (let i = 0; i < con.children.length; i++) {
                this.recursiveAddNode(con.children[i], newNode, brickDescriptor);
            }
        }

        return newNode;
    },

    addLayoutChild: function (type, initData, clazz) {
        var nodeLabel = t(type);

        if (initData) {
            if (initData.title) {
                nodeLabel = initData.title;
            } else if (initData.name) {
                nodeLabel = initData.name;
            }
        }

        var children = [];
        if (type == "classificationstore") {
            children = typeof initData.activeGroupDefinitions == "object"
                ? Object.values(initData.activeGroupDefinitions)
                : [];
        } else {
            children = initData.children;
        }

        let newNode = {
            type: "layout",
            expanded: true,
            expandable: children.length,
            allowDrag: false,
            iconCls: "pimcore_icon_" + type,
            text: nodeLabel
        };

        newNode = this.appendChild(newNode);

        this.expand();

        if (type === "classificationstore") {
            for (var groupId in initData.activeGroupDefinitions) {
                var activeGroupDefinition = initData.activeGroupDefinitions[groupId];

                var groupNode = clazz.addLayoutChild.call(newNode, "keys", {
                    title: activeGroupDefinition.name + " (" + groupId + ")",
                    children: activeGroupDefinition.keys
                }, clazz);

                Ext.Array.forEach(activeGroupDefinition.keys, function (keyData) {
                    keyData.definition.title = keyData.name;
                    var classificationDescriptor = {
                        keyConfig: {
                            id: keyData.id,
                            name: keyData.name,
                            // description: keyData.description
                        }
                    };
                    clazz.addDataChild.call(groupNode, keyData.definition.fieldtype, keyData.definition, clazz.showFieldName, {}, classificationDescriptor);
                }, this);
            }
        }

        return newNode;
    },

    addDataChild: function (type, initData, showFieldname, brickDescriptor, classificationDescriptor) {

        if (type != "objectbricks" && !initData.invisible) {
            var isLeaf = true;
            var draggable = true;

            // localizedfields can be a drop target
            if (type == "localizedfields") {

                isLeaf = false;
                draggable = false;

                Ext.apply(brickDescriptor, {
                    insideLocalizedFields: true
                });

            }

            var key = initData.name;

            if (brickDescriptor && brickDescriptor.insideBrick) {
                if (brickDescriptor.insideLocalizedFields) {
                    var parts = {
                        containerKey: brickDescriptor.brickType,
                        fieldname: brickDescriptor.brickField,
                        brickfield: key
                    }
                    key = "?" + Ext.encode(parts) + "~" + key;
                } else {
                    key = brickDescriptor.brickType + "~" + key;
                }
            }

            if (classificationDescriptor && !Ext.Object.isEmpty(classificationDescriptor)) {
                key = "#cs#" + classificationDescriptor.keyConfig.id + "#" + classificationDescriptor.keyConfig.name
            }

            var text = t(initData.title);
            if (showFieldname) {
                if (brickDescriptor && brickDescriptor.insideBrick && brickDescriptor.insideLocalizedFields) {
                    text = text + "(" + brickDescriptor.brickType + "." + initData.name + ")";
                } else {
                    text = text + " (" + key.replace(/~|\#cs\#|\#"/, ".") + ")";
                }
            }
            var newNode = {
                text: text,
                key: key,
                type: "data",
                layout: initData,
                leaf: isLeaf,
                allowDrag: draggable,
                dataType: type,
                iconCls: "pimcore_icon_" + type,
                expanded: true,
                brickDescriptor: brickDescriptor,
                classificationDescriptor: classificationDescriptor
            };

            newNode = this.appendChild(newNode);

            if (this.rendered) {
                this.expand();
            }

            return newNode;
        } else {
            return null;
        }

    },

    getClassTree: function (url, classId, objectId, targetObjectId) {

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

        var tree = new Ext.tree.TreePanel({
            title: t('class_attributes'),
            iconCls: 'pimcore_icon_gridconfig_class_attributes',
            tbar: headerConfig,
            region: "center",
            autoScroll: true,
            rootVisible: false,
            bufferedRenderer: false,
            animate: false,
            root: {
                id: "0",
                root: true,
                text: t("base"),
                allowDrag: false,
                leaf: true,
                isTarget: true
            },
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    enableDrag: true,
                    enableDrop: false,
                    ddGroup: "columnconfigelement"
                }
            }
        });

        Ext.Ajax.request({
            url: url,
            params: {
                id: classId,
                oid: objectId,
                target_oid: targetObjectId
            },
            success: this.initLayoutFields.bind(this, tree)
        });

        filterField.on("keyup", this.updateFilter.bind(this, tree, filterField));
        filterButton.on("click", this.updateFilter.bind(this, tree, filterField));

        return tree;
    },

});
