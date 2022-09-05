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


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.Bundle");

pimcore.bundle.outputDataConfigToolkit.Bundle = Class.create({

    getClassName: function () {
        return "pimcore.bundle.outputDataConfigToolkit.Bundle";
    },

    initialize: function() {
        document.addEventListener(pimcore.events.postOpenObject, this.onPostOpenObject.bind(this));
    },


    uninstall: function() {
        
    },

    onPostOpenObject: function (e) {
        let object = e.detail.object;
        let type = e.detail.type;

        if (pimcore.globalmanager.get("user").isAllowed("bundle_outputDataConfigToolkit")) {
            Ext.Ajax.request({
                url: "/admin/outputdataconfig/admin/initialize",
                params: {
                    id: object.id
                }
            })
                .then(function (res) {
                    var data = JSON.parse(res.responseText);

                    if (!data.success || data.object === false) {
                        return;
                    }

                    var objectData = object;
                    if (data.object.id) {
                        objectData = data.object;
                    }

                    var configTab = new pimcore.bundle.outputDataConfigToolkit.Tab(objectData, type);
                    var objectTabPanel = object.tab.items.items[1];

                    objectTabPanel.insert(objectTabPanel.items.length, configTab.getLayout());
                    pimcore.layout.refresh();

                }.bind(this));
        }
    }
});

var outputDataConfigToolkitPlugin = new pimcore.bundle.outputDataConfigToolkit.Bundle();
