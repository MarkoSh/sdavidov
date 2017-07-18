{*
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
*  @author    SeoSA<885588@bk.ru>
*  @copyright 2012-2017 SeoSA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{literal}
<div ng-controller="YMarketConfigureFormController as market">
    <panel
            class="yandex-market-form"
            no-collapse="true"
            heading="Yandex Market">

        <div class="form">
            <form-group label="Shop name" required="true">
                <input ng-required="true" type="text" class="form-control" ng-model="market.config.shop.name"
                       ng-keyup="configure.saveConfigurationValueDelayed('market_shop_name', market.config.shop.name)">
            </form-group>
            <div class="row" ng-repeat="bool_fieldset in market.bool_fieldsets">
                <div class="col-md-4" ng-repeat="bool_field in bool_fieldset">
                    <form-group label="{{ bool_field.label }}">
                        <prestashop-switch ng-model="market.config[bool_field.name]"
                                ng-change="configure.saveConfigurationValue('market_'+bool_field.name, market.config[bool_field.name])"></prestashop-switch>
                    </form-group>
                </div>
            </div>
            <product-filter ng-model="market.config.products_filter"
                ng-change="configure.saveProductsFilter('market_products_filter', market.config.products_filter)"
            ></product-filter>
        </div>
    </panel>
    <panel heading="Fields">
        <table class="table">
            <thead>
                <tr>
                    <th translate="Field"></th>
                    <th type="Default behaviour"></th>
                    <th translate="Include to feed"></th>
                    <th translate="Allow override"></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="(field_name, field) in market.offer_fields" ng-if="!field.no_conf">
                    <!--<td ng-bind22="field.label | translate"><span>12345</span></td>-->
                    <td ng-class="{wrap_popup: (field.info)}">
                        <span translate="{{field.label}}"></span>
                        <section ng-show="field.info" translate="{{field.info}}"></section>
                    </td>
                    <td>
                        <p ng-if="field.default_behaviour.text" ng-bind="field.default_behaviour.text"></p>
                        <select ng-if="field.default_behaviour.select"
                                ng-model="market.config.fields[field_name].default_behaviour"
                                ng-change="configure.saveConfigurationValue(
                                    'market_'+field_name+'_default_behaviour',
                                    market.config.fields[field_name].default_behaviour
                                )"
                                ng-options="value as key for (value, key) in field.default_behaviour.select">
                        </select>
                    </td>
                    <td>
                        <prestashop-switch
                                ng-if="!field.required"
                                ng-model="market.config.fields[field_name].include_to_feed"
                                ng-change="configure.saveConfigurationValue(
                                    'market_'+field_name+'_include_to_feed',
                                    market.config.fields[field_name].include_to_feed
                                )"></prestashop-switch>
                    </td>
                    <td>
                        <prestashop-switch
                                ng-if="!field.no_override && market.config.fields[field_name].include_to_feed"
                                ng-model="market.config.fields[field_name].allow_override"
                                ng-change="configure.saveConfigurationValue(
                                    'market_'+field_name+'_allow_override',
                                    market.config.fields[field_name].allow_override
                                )"></prestashop-switch>
                    </td>
                </tr>
            </tbody>
        </table>
    </panel>
    <panel heading="Category aliases">
        <table class="table">
            <thead>
            <tr>
                <th translate="Category"></th>
                <th translate="Alias"></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="category in market.categories">
                <td ng-bind="category.name"></td>
                <td>
                    <input class="form-control" type="text" ng-model="market.category_aliases[category.id_category]"/>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="form-group clearfix">
            <div class="col-lg-12 text-right">
                <button ng-click="market.saveCategoryAliases()" class="btn btn-success">
                    {{'Save' | translate}}
                </button>
            </div>
        </div>
    </panel>
    <panel ng-if="market.config.export_features" heading="Features">
        <table class="table">
            <thead>
            <tr>
                <th translate="Feature"></th>
                <th translate="Override name"></th>
                <th translate="Unit"></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="feature in market.features">
                <td ng-bind="feature.name"></td>
                <td>
                    <input class="form-control" type="text" ng-model="market.market_features[feature.id_feature].name"/>
                </td>
                <td>
                    <input class="form-control" type="text" ng-model="market.market_features[feature.id_feature].unit"/>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="form-group clearfix">
            <div class="col-lg-12 text-right">
                <button ng-click="market.saveMarketFeatures()" class="btn btn-success">
                    {{'Save' | translate}}
                </button>
            </div>
        </div>
    </panel>
    <panel ng-if="market.config.export_combinations" heading="Group attributes">
        <table class="table">
            <thead>
            <tr>
                <th translate="Group attribute"></th>
                <th translate="Override name"></th>
                <th translate="Unit"></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="attribute_group in market.attribute_groups">
                <td ng-bind="attribute_group.name"></td>
                <td>
                    <input class="form-control" type="text" ng-model="market.market_attribute_groups[attribute_group.id_attribute_group].name"/>
                </td>
                <td>
                    <input class="form-control" type="text" ng-model="market.market_attribute_groups[attribute_group.id_attribute_group].unit"/>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="form-group clearfix">
            <div class="col-lg-12 text-right">
                <button ng-click="market.saveMarketAttributeGroups()" class="btn btn-success">
                    {{'Save' | translate}}
                </button>
            </div>
        </div>
    </panel>
    <panel heading="Book on the Market">
        <div class="form-group clearfix">
            <div class="col-lg-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th translate="Category"></th>
                        <th translate="ID"></th>
                        <th translate="Instock"></th>
                        <th translate="Booking"></th>
                        <th translate="Action"></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="outlet in market.outlets">
                            <td>
                                {{market.format_categories[outlet.id_category]}}
                                <input type="hidden" ng-model="outlet.id_category">
                            </td>
                            <td>
                                <input class="form-control" type="text" ng-model="outlet.id">
                            </td>
                            <td>
                                <input class="form-control" type="text" ng-model="outlet.instock">
                            </td>
                            <td>
                                <prestashop-switch ng-model="outlet.booking"></prestashop-switch>
                            </td>
                            <td>
                                <button ng-click="market.removeOutlet($index)" class="btn btn-danger">
                                    <i class="icon-remove"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="col-lg-12">
                <select ng-model="market.category_outlet" name="category_outlet">
                    <option ng-repeat="category in market.categories" value="{{category.id_category}}">{{category.name}}</option>
                </select>
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="col-lg-12 text-right">
                <button ng-click="market.addOutlet()" class="btn btn-default">
                    <i class="icon-plus"></i>
                    {{'Add outlet' | translate}}
                </button>
                <button ng-click="market.saveOutlets()" class="btn btn-success">
                    {{'Save' | translate}}
                </button>
            </div>
        </div>
    </panel>
</div>
{/literal}