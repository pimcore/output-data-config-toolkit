/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


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
