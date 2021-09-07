This plugin adds the option of configuring a minimum order value for each channel.

When a minimum order value is configured
it is not possible to checkout the order if the subtotal of all of the items in the cart is lower the configured minimum.

#### Configuration by administrator:
Configuring a minimum order value is possible by logging in as an administrator and navigating to the channel configuration:

![admin_menu](admin_menu.png)

Choose to edit an existing channel or to add a new one:

![admin_channel_overview](admin_channel_overview.png)

This plugin adds a segment to the add/edit pane that can be used to set the desired minimum order value in the base currency:

![admin_channel_details](admin_channel_details.png)

By default the toggle is set to the off position and the field to enter the minimum order value is disabled.
To configure the desired value use the toggle to enable the minimum order value and enter the value in the field.

#### What do visitors see?

When visiting the shop and not having added enough items to the shopping cart they will not be able to proceed to checkout:

![shop_cart_summary](shop_cart_summary.png)

The checkout button is disabled and a message is displayed, providing more insight in the current status:

![shop_minimum_order_value_message](shop_minimum_order_value_message.png)

When clicking the cart widget button, the checkout button is hidden:

![shop_cart_widget](shop_cart_widget.png)

If a visitor tries to skip ahead to the checkout process (for example by entering the url manually), the visitor
will automatically be redirected to the cart summary page.

#### How does it work?

Technically what this plugin does is a few things. It adds an optional field to the channel entity and the option to configure
the fields value through the admin system.

This value is used by the MinimumOrderValueReached class, which is configured as a guard on all of the transitions of the "sylius_order_checkout"
state machine.

By overriding the "sylius.resolver.checkout" service and disabling the original service, the plugin is able to redirect the
visitor to the cart summary page when the guard blocks the transition.

Overriding that service also means that the service needs to be configured slightly differently then before (only applicable if you have a customised checkout resolver configuration):

Whereas before the the checkout resolver could be configured through key "sylius_shop", it now needs to be done through key "nedac_sylius_minimum_order_value":
```yaml
nedac_sylius_minimum_order_value:
  checkout_resolver:
    pattern: /checkout/.+
    route_map:
      empty_order:
        route: sylius_shop_cart_summary
      cart:
        route: sylius_shop_checkout_address
      addressed:
        route: sylius_shop_checkout_select_shipping
      shipping_selected:
        route: sylius_shop_checkout_select_payment
      shipping_skipped:
        route: sylius_shop_checkout_select_payment
      payment_selected:
        route: sylius_shop_checkout_complete
      payment_skipped:
        route: sylius_shop_checkout_complete
```
This is the default configuration added by this plugin in `src/Resources/config/config.yaml`. It can be overridden on an 
application level as with any Symfony bundle configuration.

Please see the official Sylius docs on how to configure the checkout resolver, should there be any need to do so.

##### Supported Sylius versions:
<table>
    <tr><td>1.10</td></tr>
</table>

> **_NOTE:_** *This plugin requires PHP 7.4 or up*

#### Installation:
1. Install using composer:
    ```bash
    composer require nedac/sylius-minimum-order-value-plugin
    ```

2. Generate and run database migration:
    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

3. Install assets:
    ```bash
    bin/console sylius:install:assets
    ```

The flex recipe should take care of enabling the bundle, copying the configuration file and copying the templates that
need to be overridden. It is quite possible that you're already overriding one or more templates.
In that case you'll have to edit/merge those templates manually with the templates that can be found in
`src/Resources/templates`.
