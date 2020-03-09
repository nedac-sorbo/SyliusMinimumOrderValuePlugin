#### Installation:
1. Install using composer:
    ```bash
    composer require nedac/sylius-minimum-order-value-plugin
    ```

2. Add to bundles.php:
    ```php
    # config/bundles.php
    <?php

    return [
        # ...
        Nedac\SyliusMinimumOrderValuePlugin\NedacSyliusMinimumOrderValuePlugin::class => ['all' => true],
    ];
    ```

3. Import configuration file:
    ```yaml
    # config/packages/_sylius.yaml
    imports:
       # ...

       - { resource: "@NedacSyliusMinimumOrderValuePlugin/Resources/config/config.yaml" }

    # ...
    ```

4. Implement the interface and use the trait in the Channel model:
    ```php
    # src/entity/Channel/Channel.php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Channel;

    use Doctrine\ORM\Mapping as ORM;
    use Nedac\SyliusMinimumOrderValuePlugin\Model\ChannelInterface as NedacSyliusMinimumOrderValuePluginChannelInterface;
    use Nedac\SyliusMinimumOrderValuePlugin\Model\MinimumOrderValueTrait as
        NedacSyliusMinimumOrderValuePluginMinimumOrderValueTrait;
    use Sylius\Component\Core\Model\Channel as BaseChannel;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_channel")
     */
    class Channel extends BaseChannel implements NedacSyliusMinimumOrderValuePluginChannelInterface
    {
        use NedacSyliusMinimumOrderValuePluginMinimumOrderValueTrait;

        // ...
    }
    ```

5. Generate and run database migration:
    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

6. Override templates:
    ```twig
    {# templates/bundles/SyliusAdminBundle/Channel/_form.html.twig #}

    {{ form_errors(form) }}
    <div class="ui two column stackable grid">
        <div class="column">
            <div class="ui segment">
                {{ form_errors(form) }}
                <div class="two fields">
                    {{ form_row(form.code) }}
                    {{ form_row(form.name) }}
                </div>
                {{ form_row(form.description) }}
                {{ form_row(form.enabled) }}
                <div class="two fields">
                    <div class="field">
                        {{ form_label(form.hostname) }}
                        <div class="ui labeled input">
                            <div class="ui label">http://</div>
                            {{ form_widget(form.hostname) }}
                        </div>
                        {{ form_errors(form.hostname) }}
                    </div>
                    {{ form_row(form.contactEmail) }}
                </div>
                <div class="two fields">
                    {{ form_row(form.color) }}
                    {{ form_row(form.themeName) }}
                </div>
            </div>
            <div class="ui segment">
                <h4 class="ui dividing header">{{ form_label(form.shopBillingData) }}</h4>
                <div class="two fields">
                    {{ form_row(form.shopBillingData.company) }}
                    {{ form_row(form.shopBillingData.taxId) }}
                </div>
                <div class="two fields">
                    {{ form_row(form.shopBillingData.countryCode) }}
                    {{ form_row(form.shopBillingData.street) }}
                </div>
                <div class="two fields">
                    {{ form_row(form.shopBillingData.city) }}
                    {{ form_row(form.shopBillingData.postcode) }}
                </div>
            </div>
        </div>
        <div class="column">
            <div id="nedac-sylius-minimum-order-value-plugin-admin-before" class="ui segment">
                {{ form_row(form.locales) }}
                {{ form_row(form.defaultLocale) }}
                {{ form_row(form.currencies) }}
                {{ form_row(form.baseCurrency) }}
                {{ form_row(form.defaultTaxZone) }}
                {{ form_row(form.taxCalculationStrategy) }}
                {{ form_row(form.skippingShippingStepAllowed) }}
                {{ form_row(form.skippingPaymentStepAllowed) }}
                {{ form_row(form.accountVerificationRequired) }}
            </div>
            {% if form.minimumOrderValue is defined %}
                <div id="nedac-sylius-minimum-order-value-plugin-admin-segment" class="ui segment">
                    {{ form_row(form.minimumOrderValue) }}
                    <div class="ui toggle checkbox">
                        <input id="nedac-sylius-minimum-order-value-plugin-admin-toggle" type="checkbox" name="public">
                        <label>{{ 'nedac_sylius_minimum_order_value_plugin.ui.enabled'|trans }}</label>
                    </div>
                </div>
            {% endif %}
        </div>
    </div>
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Cart/Summary/_checkout.html.twig #}

    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

    {% set minimumOrderValue = cart.channel.minimumOrderValue %}
    {% set total = sylius_order_items_subtotal(cart) %}

    {% if total >= minimumOrderValue %}
        {% set buttonClass = "ui huge primary fluid labeled icon button" %}
    {% else %}
        {% set buttonClass = "ui huge primary fluid labeled icon button disabled" %}
        {% set formattedMinimum = money.convertAndFormat(minimumOrderValue) %}
        {% set formattedDifference = money.convertAndFormat(minimumOrderValue - total) %}
        <div class="ui icon negative message">
            <i class="warning icon"></i>
            <div class="content">
                <div class="header">{{ 'nedac_sylius_minimum_order_value_plugin.ui.attention'|trans }}</div>
                <p>{{ 'nedac_sylius_minimum_order_value_plugin.ui.minimum_not_yet_reached'|trans({ '%minimumOrderValue% ': formattedMinimum, '%difference%': formattedDifference })|raw('br') }}</p>
            </div>
        </div>
    {% endif %}
    <a href="{{ path('sylius_shop_checkout_start') }}" class="{{ buttonClass }}" id="nedac-checkout-button"><i class="check icon"></i> {{ 'sylius.ui.checkout'|trans }}</a>
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Cart/_widget.html.twig #}

    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

    {% set minimumOrderValue = cart.channel.minimumOrderValue %}
    {% set total = sylius_order_items_subtotal(cart) %}

    <div id="sylius-cart-button" class="ui circular cart button">
        {{ sonata_block_render_event('sylius.shop.partial.cart.summary.before_widget_content', {'cart': cart}) }}

        <i class="cart icon"></i>
        <span id="sylius-cart-total">
            {{ money.convertAndFormat(cart.itemsTotal) }}
        </span>
        {% transchoice cart.items|length %}sylius.ui.item.choice{% endtranschoice %}

        {{ sonata_block_render_event('sylius.shop.partial.cart.summary.after_widget_content', {'cart': cart}) }}
    </div>
    <div class="ui large flowing cart hidden popup">
        {{ sonata_block_render_event('sylius.shop.partial.cart.summary.before_popup_content', {'cart': cart}) }}

        {% if cart.empty %}
            {{ 'sylius.ui.your_cart_is_empty'|trans }}.
        {% else %}
            <div class="ui list">
                {% for item in cart.items %}
                    <div class="item">{{ item.quantity }} x <strong>{{ item.product }}</strong> {{ money.convertAndFormat(item.unitPrice) }}</div>
                {% endfor %}
                <div class="item"><strong>{{ 'sylius.ui.subtotal'|trans }}</strong>: {{ money.convertAndFormat(cart.itemsTotal) }}</div>
            </div>
            <a href="{{ path('sylius_shop_cart_summary') }}" class="ui fluid basic text button">{{ 'sylius.ui.view_and_edit_cart'|trans }}</a>
            {% if total >= minimumOrderValue %}
                <div class="ui divider"></div>
                <a href="{{ path('sylius_shop_checkout_start') }}" class="ui fluid primary button">{{ 'sylius.ui.checkout'|trans }}</a>
            {% endif %}
        {% endif %}

        {{ sonata_block_render_event('sylius.shop.partial.cart.summary.after_popup_content', {'cart': cart}) }}
    </div>
    ```

#### Setup development environment:
```bash
docker-compose build
docker-compose up -d
docker-compose exec php composer --working-dir=/srv/sylius install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application build
docker-compose exec php bin/console assets:install public
docker-compose exec php bin/console doctrine:schema:create
docker-compose exec php bin/console sylius:fixtures:load -n
```
#### Running tests:
```bash
docker-compose exec php sh
cd ../..
vendor/bin/behat
```
