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

<div id="sya-market" class="seosayandexservices">
    {include "../../_angular_translations.tpl"}
    {include "../../_angular_templates.tpl"}
    {include "../../_angular_variables.tpl"}
    <script type="application/javascript">
        (function () {
            angular.module('sya.market')
                    .constant('market_product_overrides', {$market_product_overrides|sya_json_encode});
            angular.module('sya.market')
                    .constant('product_outlets', {$product_outlets|sya_json_encode});
        })();
    </script>

    {literal}
        {{pf.offer_fields | json}}
        <div ng-controller="YMarketProductFormController as pf">
            <panel heading="Yandex Market properties" class="custom_bootstrap">
                <div class="row">
                    <div class="col-md-7">
                        <market-product-override-form-group
                                ng-repeat="(field_name, field) in pf.offer_fields"
                                ng-if="pf.isOverrideAllowed(field_name) && field.value.type !== 'bool'"></market-product-override-form-group>
                    </div>
                    <div class="col-md-4 col-md-offset-1">
                        <market-product-override-form-group
                                ng-repeat="(field_name, field) in pf.offer_fields"
                                ng-if="pf.isOverrideAllowed(field_name) && field.value.type === 'bool'"></market-product-override-form-group>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label translate="Book on the market"></label>
                    <div class="col-lg-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th translate="ID"></th>
                                    <th translate="Instock"></th>
                                    <th translate="Booking"></th>
                                    <th translate="Action"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="outlet in pf.product_outlets">
                                    <td>
                                        <input type="hidden" ng-model="outlet.id_product">
                                        <input ng-change="pf.saveProductOutletsDelayed()" class="form-control" type="text" ng-model="outlet.id">
                                    </td>
                                    <td>
                                        <input ng-change="pf.saveProductOutletsDelayed()" class="form-control" type="text" ng-model="outlet.instock">
                                    </td>
                                    <td>
                                        <prestashop-switch ng-change="pf.saveProductOutletsDelayed()" ng-model="outlet.booking"></prestashop-switch>
                                    </td>
                                    <td>
                                        <button type="button" ng-click="pf.removeProductOutlet($index)" class="btn btn-danger">
                                            <i class="icon-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-lg-12">
                                <button ng-click="pf.addProductOutlet()" type="button" class="btn btn-default">
                                    <i class="icon-plus"></i>
                                    {{'Add outlet' | translate}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </panel>
        </div>
    {/literal}

    <script>
        __initialize_seosayandexservices("sya.market", "sya-market");
    </script>
</div>

