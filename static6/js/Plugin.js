pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.Plugin");

pimcore.plugin.outputDataConfigToolkit.Plugin = Class.create(pimcore.plugin.admin, {


    getClassName: function () {
        return "pimcore.plugin.outputDataConfigToolkit.Plugin";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },


    uninstall: function() {
        
    },

    postOpenObject: function(object, type) {
        if(pimcore.globalmanager.get("user").isAllowed("plugin_outputDataConfigToolkit")) {
            var configTab = new pimcore.plugin.outputDataConfigToolkit.Tab(object, type);
            object.tab.items.items[1].insert(object.tab.items.items[1].items.length, configTab.getLayout());
            pimcore.layout.refresh();
        }
    }
});

new pimcore.plugin.outputDataConfigToolkit.Plugin();
