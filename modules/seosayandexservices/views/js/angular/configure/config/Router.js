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
    angular.module('sya.configure').config(RouterConfigurator);

    RouterConfigurator.$inject = ['RouterProvider','yandex_components', 'api_url'];

    function RouterConfigurator(RouterProvider, yandex_components, api_url) {
        RouterProvider.setBaseUrl(api_url);
        RouterProvider.setDefaultRoute('welcome');
        RouterProvider.addRoute('welcome', 'Yandex services');

        RouterProvider.addRoute('documentation', 'Yandex services documentation');

        for (var component_name in yandex_components)
        {
            if (!yandex_components.hasOwnProperty(component_name)) continue;

            var component = yandex_components[component_name];

            if (component['has_config_form'])
                RouterProvider.addRoute('configure-'+component.name, 'Configure '+component.name);

            if (component['angular_routes']) {
                for (var name in component['angular_routes']) {
                    if (!component['angular_routes'].hasOwnProperty(name)) continue;
                    RouterProvider.addRoute(name, component['angular_routes'][name])
                }
            }
        }
    }
})();