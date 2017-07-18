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
    angular.module('sya.configure')
        .controller('ConfigureController', ConfigureController);


    ConfigureController.$inject = ['$rootScope', 'Router', 'ComponentsManager', 'Api', '$timeout', 'Notifier', 'PromiseQueue'];
    function ConfigureController($rootScope, Router, ComponentsManager, Api, $timeout, Notifier, PromiseQueue) {
        $rootScope.$on('stateChangeError', function () {
            Router.setState('welcome')
        });

        this.router = Router;

        this.components = ComponentsManager.getComponents();

        var api_queue = new PromiseQueue(Api);
        var timeout_queue = new PromiseQueue($timeout);

        this.isComponentEnabled = function (name) {
            return this.components[name] && this.components[name].enabled;
        };

        this.saveConfigurationValue = function (name, value, html, escape) {
            if (typeof value == 'object')
            {
                console.log(value);
                var tmp = {};
                angular.forEach(value, function (item, key) {
                    if (item != null)
                        tmp[key] = item;
                });
                value = tmp;
            }
            var ajax_key = 'saveConfigurationValue-'+name;
            api_queue.cancel(ajax_key);
            api_queue.add(
                ajax_key,
                Api.post('update_configuration_value', {
                    name: name,
                    html: !!html,
                    escape: !!escape,
                    value: value
                }).then(function () {
                    Notifier.notify('Configuration saved!');
                })
            )
        };

        this.saveProductsFilter = function (name, filter) {
            var ajax_key = 'saveConfigurationValue-'+name;
            api_queue.cancel(ajax_key);
            api_queue.add(
                ajax_key,
                Api.post('update_products_filter_configuration_value', {
                    name: name,
                    mode: filter.mode,
                    items: filter.items
                }).then(function () {
                    Notifier.notify('Configuration saved!');
                })
            )
        };

        this.saveConfigurationValueDelayed = function (name, value, delay, html, escape) {
            var timeout_key = 'saveConfigurationValueDelayed-'+name;
            timeout_queue.cancel(timeout_key);

            var that = this;
            timeout_queue.add(
                timeout_key,
                $timeout(function () {
                    that.saveConfigurationValue(name, value, html, escape)
                }, delay || 500)
            );
        };

        this.setComponentStatus = function (component, status) {
            var ajax_key = 'setModuleStatus-'+component;
            api_queue.cancel(ajax_key);
            api_queue.add(
                ajax_key,
                Api.post(status ? 'enable_component' : 'disable_component', {
                    component: component
                }).then(function () {
                    Notifier.notify('Configuration saved!');
                })
            )
        };
    }
})();