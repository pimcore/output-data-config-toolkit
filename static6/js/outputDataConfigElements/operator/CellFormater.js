pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.CellFormater");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.CellFormater = Class.create(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.Abstract, {
    type: "operator",
    class: "CellFormater",
    iconCls: "pimcore_icon_operator_cell_formater",
    defaultText: "operator_cell_formater",


    getConfigTreeNode: function(configAttributes) {
        if(configAttributes) {
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: configAttributes.label,
                configAttributes: configAttributes,
                isTarget: true,
                maxChildCount: 1,
                expanded: true,
                leaf: false,
                expandable: false
            };
        } else {

            //For building up operator list
            var configAttributes = { type: this.type, class: this.class};

            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText),
                configAttributes: configAttributes,
                isTarget: true,
                maxChildCount: 1,
                leaf: true
            };
        }
        return node;
    },


    getCopyNode: function(source) {
        var copy = source.createNode({
            iconCls: this.iconCls,
            text: source.data.cssClass,
            isTarget: true,
            leaf: false,
            maxChildCount: 1,
            expanded: true,
            configAttributes: {
                label: null,
                type: this.type,
                class: this.class
            }
        });
        return copy;
    },


    getConfigDialog: function(node) {
        this.node = node;

        this.cssClass = new Ext.form.TextField({
            fieldLabel: t('operator_cell_formater_css_class'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.cssClass
        });

        this.styles = new Ext.form.TextArea({
            fieldLabel: t('operator_cell_formater_css_styles'),
            length: 255,
            width: 200,
            height: 100,
            value: this.node.data.configAttributes.styles
        });


        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.cssClass, this.styles],
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
            height: 350,
            modal: true,
            title: t('operator_cell_formater_settings'),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
        return this.window;
    },

    commitData: function() {
        this.node.data.configAttributes.cssClass = this.cssClass.getValue();
        this.node.data.configAttributes.styles = this.styles.getValue();
        this.node.data.configAttributes.label = this.cssClass.getValue();
        this.node.set('text', this.cssClass.getValue());
        this.window.close();
    }
});