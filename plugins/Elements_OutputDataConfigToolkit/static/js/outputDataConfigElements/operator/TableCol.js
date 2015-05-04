pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.TableCol");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.TableCol = Class.create(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Group, {
    type: "operator",
    class: "TableCol",
    iconCls: "pimcore_icon_operator_tablecol",
    defaultText: "operator_tablecol",
    windowTitle: "tablecol_operator_settings",
    allowedTypes: null,
    allowedParents: {
        operator: {
            TableRow: true
        }
    },
    maxChildCount: 1,


    getConfigTreeNode: function(configAttributes) {
        if(configAttributes) {
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText) + " (" + configAttributes.colspan + ")",
                configAttributes: configAttributes,
                isTarget: true,
                allowChildren: true,
                allowedTypes: this.allowedTypes,
                allowedParents: this.allowedParents,
                maxChildCount: this.maxChildCount,
                expanded: true,
                leaf: false
            };
        } else {

            //For building up operator list
            var configAttributes = { type: this.type, class: this.class};

            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText),
                configAttributes: configAttributes,
                allowedTypes: this.allowedTypes,
                allowedParents: this.allowedParents,
                maxChildCount: this.maxChildCount,
                isTarget: true,
                leaf: true
            };
        }
        return node;
    },


    getConfigDialog: function(node) {
        this.node = node;

        var value = 1;
        if(this.node.attributes.configAttributes.colspan) {
            value = this.node.attributes.configAttributes.colspan;
        }

        this.numberfield = new Ext.form.NumberField({
            fieldLabel: t('colspan'),
            length: 255,
            width: 200,
            value: value
        });

        this.headline = new Ext.form.Checkbox({
            fieldLabel: t('headline'),
            checked: this.node.attributes.configAttributes.headline
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.numberfield, this.headline],
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
    },

    commitData: function() {
        this.node.attributes.configAttributes.colspan = this.numberfield.getValue();
        this.node.attributes.configAttributes.headline = this.headline.getValue();
        this.node.setText( this.node.attributes.text + " (" + this.numberfield.getValue() + ")" );
        this.window.close();
    }

});