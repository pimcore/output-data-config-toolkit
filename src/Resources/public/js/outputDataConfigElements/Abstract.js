/**
* This source file is available under the terms of the
* Pimcore Open Core License (POCL)
* Full copyright and license information is available in
* LICENSE.md which is distributed with this source code.
*
*  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.com)
*  @license    Pimcore Open Core License (POCL)
*/


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.Abstract");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.Abstract = Class.create({
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
            text: source.data.text,
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