/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    SeoSA <885588@bk.ru>
 *  @copyright 2012-2017 SeoSA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

(function () {
    angular.module('sya.market')
        .controller('YMarketProductFormController', YMarketProductFormController);

    YMarketProductFormController.$inject = ['market_yml_standard', 'product_outlets', 'market_config', 'PromiseQueue', 'Api', '$timeout', 'Notifier', 'market_product_overrides'];
    function YMarketProductFormController(market_yml_standard, product_outlets, market_config, PromiseQueue, Api, $timeout, Notifier, market_product_overrides) {
        this.product = market_product_overrides;
        this.product_outlets = product_outlets;

        if (angular.isString(this.product['delivery-options']))
            this.product['delivery-options'] = [];

        this.standard = market_yml_standard;
        this.offer_fields = $.extend(true, {},
            market_yml_standard.yml_catalog.children.shop.children.offers.children.offer.attributes,
            market_yml_standard.yml_catalog.children.shop.children.offers.children.offer.children,
            market_yml_standard.yml_catalog.children.shop.children.offers.children.offer.children['delivery-options'].children
        );

        this.isOverrideAllowed = function (field) {
            var def = this.offer_fields[field];

            if (def.no_conf || def.no_override || (!def.value && !def.children))
                return false;
            return market_config.fields[field] && market_config.fields[field].allow_override;
        };

        var api_queue = new PromiseQueue(Api);
        var timeout_queue = new PromiseQueue($timeout);


        this.saveOverrideValue = function (name, value) {
            var ajax_key = 'saveOverrideValue-'+name;
            api_queue.cancel(ajax_key);
            api_queue.add(
                ajax_key,
                Api.post(['market', 'update_override_value'], {
                    name: name,
                    value: value,
                    id_product: window.id_product
                }).then(function () {
                    Notifier.notify('Configuration saved!');
                })
            )
        };

        this.saveOverrideValueDelayed = function (name, value, delay) {
            var timeout_key = 'saveOverrideValueDelayed-'+name;
            timeout_queue.cancel(timeout_key);

            var that = this;
            timeout_queue.add(
                timeout_key,
                $timeout(function () {
                    that.saveOverrideValue(name, value)
                }, delay || 500)
            );
        };

        this.addDeliveryOption = function (field_name)
        {
            if (!this.product[field_name] || angular.isString(this.product[field_name]))
                this.product[field_name] = [];

            this.product[field_name].push({
                "cost": '',
                "days": '',
                "order_before": ''
            });
        };

        this.deleteDeliveryOption = function (index)
        {
            this.product['delivery-options'].splice(index, 1);
            this.saveOverrideValue('delivery-options', this.product['delivery-options']);
        };

        this.saveDeliveryOptionsDelayed = function ()
        {
            this.saveOverrideValueDelayed('delivery-options', this.product['delivery-options']);
        };

        this.saveProductOutletsDelayed = function ()
        {
            var timeout_key = 'saveOverrideValueDelayed-outlet';
            timeout_queue.cancel(timeout_key);

            var that = this;
            timeout_queue.add(
                timeout_key,
                $timeout(function () {
                    var ajax_key = 'saveOverrideValue-outlet';
                    api_queue.cancel(ajax_key);
                    api_queue.add(
                        ajax_key,
                        Api.post(['market', 'save_product_outlets'], {
                            product_outlets: that.product_outlets,
                            id_product: window.id_product
                        }).then(function () {
                            Notifier.notify('Configuration saved!');
                        })
                    )
                }, 500)
            );
        };

        this.removeProductOutlet = function (index) {
            this.product_outlets.splice(index, 1);
        };

        this.addProductOutlet = function () {
            this.product_outlets.push({
                id_product: window.id_product,
                id: '',
                instock: '',
                booking: ''
            });
        };
    }
})();