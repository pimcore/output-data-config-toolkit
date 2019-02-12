pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.ClassTree");
pimcore.bundle.outputDataConfigToolkit.ClassTree = Class.create(pimcore.object.helpers.classTree,{

    recursiveAddNode: function (con, scope, brickDescriptor) {

        var fn = null;
        var newNode = null;

        if (con.datatype == "layout" || con.fieldtype == "classificationstore") {
            fn = this.addLayoutChild.bind(scope, con.fieldtype, con, this);
        }
        else if (con.datatype == "data") {
            fn = this.addDataChild.bind(scope, con.fieldtype, con, this.showFieldName, brickDescriptor, this);
        }

        newNode = fn();

        if (con.childs) {
            for (var i = 0; i < con.childs.length; i++) {
                this.recursiveAddNode(con.childs[i], newNode, brickDescriptor);
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
            children = Object.values(initData.activeGroupDefinitions);
        } else {
            children = initData.childs;
        }

        var newNode = {
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
            console.log(type);
            console.log(initData);
            console.log(initData.activeGroupDefinitions);

            for (var groupId in initData.activeGroupDefinitions) {
                var activeGroupDefinition = initData.activeGroupDefinitions[groupId];
                Ext.Array.forEach(activeGroupDefinition.keys, function (keyData) {
                    console.log(keyData);
                    console.log(clazz.showFieldName);
                    clazz.addDataChild.call(newNode, keyData.fieldtype, keyData, true, clazz);
                }, this);
            }
        }

        return newNode;
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