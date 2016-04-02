pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.TableRow");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.TableRow = Class.create(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Group, {
    type: "operator",
    class: "TableRow",
    iconCls: "pimcore_icon_operator_tablerow",
    defaultText: "operator_tablerow",
    windowTitle: "tablerow_operator_settings",
    allowedTypes: null,
    allowedParents: {
        operator: {
            Table: true
        }
    },

    getConfigDialog: function(node) {
        this.node = node;


        this.headline = new Ext.form.Checkbox({
            fieldLabel: t('headline'),
            checked: this.node.data.configAttributes.headline
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.headline],
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
        return this.window;
    },

    commitData: function() {
        this.node.data.configAttributes.headline = this.headline.getValue();
        this.window.close();
    }


});