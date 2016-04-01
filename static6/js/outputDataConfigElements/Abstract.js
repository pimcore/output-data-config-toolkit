pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.Abstract");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.Abstract = Class.create({
    type: null,
    class: null,
    objectClassId: null,
    allowedTypes: null,
    allowedParents: null,
    maxChildCount: null,
    
    initialize: function(classId) {
        this.objectClassId = classId;
    },

    getConfigTreeNode: function(configAttributes) {
        return {};
    },


    getCopyNode: function(source) {
        var copy = new Ext.tree.TreeNode({
            text: source.attributes.text,
            isTarget: true,
            leaf: true,
            configAttributes: {
                label: null,
                type: this.type,
                class: this.class
            }
        });
        return copy;
    },


    getConfigDialog: function(node) {
    },

    commitData: function() {
        this.window.close();
    }
});