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
        .controller('YMarketConfigureFormController', YMarketConfigureFormController);

    YMarketConfigureFormController.$inject = ['market_config', 'market_yml_standard', 'outlets', 'category_aliases', 'market_features', 'market_attribute_groups', 'categories', 'features', 'attribute_groups', 'Api', 'Notifier', '$filter'];
    function YMarketConfigureFormController(market_config, market_yml_standard, outlets, category_aliases, market_features, market_attribute_groups, categories, features, attribute_groups, Api, Notifier, $filter) {
        this.category_aliases = category_aliases;
        this.market_features = market_features;
        this.market_attribute_groups = market_attribute_groups;
        this.categories = categories;
        this.features = features;
        this.attribute_groups = attribute_groups;
        this.config = market_config;
        this.standard = market_yml_standard;
        this.outlets = outlets;
        this.category_outlet = null;
        this.$filter = $filter;
        this.offer_fields = $.extend(true, {},
            market_yml_standard.yml_catalog.children.shop.children.offers.children.offer.attributes,
            market_yml_standard.yml_catalog.children.shop.children.offers.children.offer.children
        );

        this.format_categories = {};
        var self = this;
        angular.forEach(this.categories, function (category) {
            self.format_categories[String(category.id_category)] = category.name;
        });

        this.bool_fieldsets = [
            [
                {name: 'export_combinations', label: 'Export combinations'},
                {name: 'export_features', label: 'Export features'},
                {name: 'export_all_currencies', label: 'Export all currencies'}
            ],
            [
                {name: 'exclude_not_available', label: 'Exclude not available products'},
                {name: 'exclude_disabled_categories', label: 'Exclude disabled categories'},
                {name: 'exclude_disabled_products', label: 'Exclude disabled products'}
            ],
            [
                {name: 'home_category_as_root', label: 'Home category as root'},
                {name: 'export_attributes_in_params', label: 'Export attributes in params'}
            ]
        ];


        this.saveCategoryAliases = function ()
        {
            Api.post(['market', 'save_category_aliases'], {
                category_aliases: this.category_aliases
            }).then(function () {
                Notifier.notify('Configuration saved!');
            })
        };

        this.saveMarketFeatures = function ()
        {
            Api.post(['market', 'save_market_features'], {
                market_features: this.market_features
            }).then(function () {
                Notifier.notify('Configuration saved!');
            })
        };

        this.saveMarketAttributeGroups = function () {
            Api.post(['market', 'save_market_attribute_groups'], {
                market_attribute_groups: this.market_attribute_groups
            }).then(function () {
                Notifier.notify('Configuration saved!');
            })
        };

        this.saveOutlets = function () {
            Api.post(['market', 'save_market_outlets'], {
                outlets: this.outlets
            }).then(function () {
                Notifier.notify('Configuration saved!');
            })
        };

        this.addOutlet = function () {
            if (this.category_outlet == null)
            {
                alert(this.$filter('translate')('Please select outlet category!'));
                return false;
            }

            this.outlets.push({
                id: '',
                id_category: this.category_outlet,
                instock: '',
                booking: ''
            });

            this.category_outlet = null;
        };

        this.inArrayCategory = function (id_category)
        {
            var choice = false;
            angular.forEach(this.outlets, function (outlet) {
                if (parseInt(outlet.id_category) == parseInt(id_category))
                    choice = true;
            });
            return choice;
        };

        this.removeOutlet = function (index) {
            this.outlets.splice(index, 1);
        };
    }
})();