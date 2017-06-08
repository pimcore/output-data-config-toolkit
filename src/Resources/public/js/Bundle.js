/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.Bundle");

pimcore.bundle.outputDataConfigToolkit.Bundle = Class.create(pimcore.plugin.admin, {

    getClassName: function () {
        return "pimcore.bundle.outputDataConfigToolkit.Bundle";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },


    uninstall: function() {
        
    },

    postOpenObject: function(object, type) {
        if(pimcore.globalmanager.get("user").isAllowed("bundle_outputDataConfigToolkit")) {
            var configTab = new pimcore.bundle.outputDataConfigToolkit.Tab(object, type);
            object.tab.items.items[1].insert(object.tab.items.items[1].items.length, configTab.getLayout());
            pimcore.layout.refresh();
        }
    }
});

new pimcore.bundle.outputDataConfigToolkit.Bundle();
