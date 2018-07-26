# OutputDataConfigToolkit

This toolkit provides an user interface to create output formats for data objects based on different output channels.
So it is possible to define, which attributes of a data objects should be printed in a certain output channel.
An output data configuration consists of
- values = data object attributes
- operators = can combine, modify, calculate, ... values

## Configuration

After installing the bundle, a config file is located at `/var/config/outputdataconfig/config.php`. In this config file available output channels can be configured as follows:

```php
<?php
    return [
        "channels" => [
            "channel1",
            "channel2",
            "mychannel1",
            "mychannel2"
        ]
    ];
```

## Defining output data configuration for different output channels

Output data configurations can be configured in an additional tab in the data object editor.
There for each data object class and output channel an output output data configuration can be defined.

![list](doc/img/list.png)


The output data configurations can be inherited along the data objects tree. The column Object ID shows from with data object the output data configuration is inherited from.
By clicking overwrite, the editor opens and a new output data configuration can be configured.

![editor](doc/img/editor.png)


## Working with output channels in code

The bundle provides a service class, with converts a Pimcore data object to an output data structure based on its ouput data configuration.

```php
<?php

    // returns the output data structure for the given product and the output channel productdetail_specification
    $specificationOutputChannel =  OutputDataConfigToolkitBundle\Service::getOutputDataConfig($product, "productdetail_specification");

    //printing output channel in view script with view-helper
    foreach($specificationOutputChannel as $property) {
        $this->productListSpecification($property, $this->product);
    }
```

A sample template helper see `doc/ProductListSpecification.php`, the needed service configuration: 
```yml
# Product Detail Specification Template Helper
app.templating.helper.productDetailSpecification:
    class: AppBundle\Templating\Helper\ProductDetailSpecification
    arguments: ['@translator', '@pimcore.locale.intl_formatter']
    tags:
        - { name: templating.helper, alias: productDetailSpecification }
```

### used by projects for example
- E-Commerce-Demo (http://ecommercedemo.pimcore.org)

## Adding new operators
Create a Pimcore bundle and add following files:

### php implementation of operator
- must be in namespace `OutputDataConfigToolkitBundle\ConfigElement\Operator`
- must implement `AbstractOperator`


```php
<?php
namespace OutputDataConfigToolkitBundle\ConfigElement\Operator;

class RemoveZero extends AbstractOperator {


    public function __construct($config, $context = null) {
        parent::__construct($config, $context);
    }

    public function getLabeledValue($object) {
        $childs = $this->getChilds();
        if($childs[0]) {

            $value = $childs[0]->getLabeledValue($object);
            $value->value = $value->value == 0 ? null : $value->value;

            return $value;
        }
        return null;
    }

}
```

### java script implementation of operator
- must be in namespace `pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator`
- must extend `pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.Abstract`

```javascript
pimcore.registerNS("pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.RemoveZero");

pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.operator.RemoveZero = Class.create(pimcore.bundle.outputDataConfigToolkit.outputDataConfigElements.Abstract, {
    type: "operator",
    class: "RemoveZero",
    iconCls: "pimcore_icon_operator_remove_zero",
    defaultText: "operator_remove_zero",


    getConfigTreeNode: function(configAttributes) {
        if(configAttributes) {
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText),
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
        var copy = new Ext.tree.TreeNode({
            iconCls: this.iconCls,
            text: t(this.defaultText),
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
    },

    commitData: function() {
    }
});
```

## Running with Pimcore < 5.4
With Pimcore 5.4 the location of static Pimcore files like icons has changed. In order to make this bundle work 
with Pimcore < 5.4, please add following rewrite rule to your `.htaccess`.
```
    # rewrite rule for pre pimcore 5.4 core static files
    RewriteRule ^bundles/pimcoreadmin/(.*) /pimcore/static6/$1 [PT,L]
``` 


## Migration from Pimcore 4
- Change table name from `plugin_outputdataconfigtoolkit_outputdefinition` to 
`bundle_outputdataconfigtoolkit_outputdefinition`.
```sql
RENAME TABLE plugin_outputdataconfigtoolkit_outputdefinition TO bundle_outputdataconfigtoolkit_outputdefinition; 
```
- Change namespace from `Elements\OutputDataConfigToolkit` to `OutputDataConfigToolkitBundle`.
- Removed key value support.
- Changed permission key to `bundle_outputDataConfigToolkit`, execute following SQL statement
```sql
UPDATE users_permission_definitions SET `key` = REPLACE(`key`, 'plugin_outputDataConfigToolkit', 'bundle_outputDataConfigToolkit');
UPDATE users SET permissions = REPLACE(`permissions`, 'plugin_outputDataConfigToolkit', 'bundle_outputDataConfigToolkit');
```
- namespaces for custom operators and values changed from `pimcore.plugin.outputDataConfigToolkit.*` to `pimcore.bundle.outputDataConfigToolkit.*`  