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
    angular.module('sya.metrika')
        .controller('YMetrikaConfigureFormController', YMetrikaConfigureFormController);

    YMetrikaConfigureFormController.$inject = ['metrika_config'];
    function YMetrikaConfigureFormController(metrika_config) {
        this.config = metrika_config;

        this.goals = [
            {event: 'order', description: 'Reached when order is confirmed.'},
            {event: 'create_account', description: 'Reached when customer creates a new account.'},
            {event: 'add_product_to_cart', description: 'Reached when customer click on add to cart button.'},
            {event: 'remove_product_from_cart', description: 'Reached when customer removes product from cart'}
        ];
    }
})();