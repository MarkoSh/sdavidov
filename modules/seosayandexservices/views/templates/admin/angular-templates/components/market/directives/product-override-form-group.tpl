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
    <div class="form-group">
        <label ng-bind="field.label | translate"></label>
        <div  ng-switch="field.value.type">
            <div ng-switch-when="bool">
                <prestashop-switch class="form-control"
                                   model-nullable
                                   ng-model="pf.product[field_name]"
                                   ng-change="pf.saveOverrideValue(field_name, pf.product[field_name])"></prestashop-switch>
            </div>
            <div ng-switch-when="select">
                <select class="form-control"
                        ng-model="pf.product[field_name]"
                        ng-change="pf.saveOverrideValue(field_name, pf.product[field_name])"
                        model-nullable
                        ng-options="value as (label | translate) for (value, label) in field.values">
                    <option value=""></option>
                </select>
            </div>
            <div ng-switch-when="number">
                <input class="form-control" type="number"
                       ng-model="pf.product[field_name]"
                       ng-change="pf.saveOverrideValueDelayed(field_name, pf.product[field_name])"
                       model-nullable/>
            </div>
            <div ng-switch-when="text">
                            <textarea class="form-control" type="text"
                                      ng-model="pf.product[field_name]"
                                      ng-change="pf.saveOverrideValueDelayed(field_name, pf.product[field_name])"
                                      model-nullable></textarea>
            </div>
            <div ng-if="!field.children" ng-switch-default>
                <input class="form-control" type="text"
                       ng-model="pf.product[field_name]"
                       ng-change="pf.saveOverrideValueDelayed(field_name, pf.product[field_name])"
                       model-nullable/>
            </div>
            <div class="form-group" ng-if="field.children && field_name == 'delivery-options'" ng-switch-default>
                <div class="col-lg-12" ng-if="(pf.product[field_name] && !angular.isString(pf.product[field_name]))">
                    <div class="form-group" ng-repeat="delivery_option in pf.product[field_name] track by $index">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-12 required" translate="Cost"></label>
                                <div class="col-lg-12">
                                    <input ng-change="pf.saveDeliveryOptionsDelayed()" ng-class="{field_error: (!delivery_option.cost)}" type="text" ng-model="delivery_option.cost">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-12 required" translate="Days"></label>
                                <div class="col-lg-12">
                                    <input ng-change="pf.saveDeliveryOptionsDelayed()" ng-class="{field_error: (!delivery_option.days)}" type="text" ng-model="delivery_option.days">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-12" translate="Order before"></label>
                                <div class="col-lg-10">
                                    <input ng-change="pf.saveDeliveryOptionsDelayed()" type="text" ng-model="delivery_option['order_before']">
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-danger" ng-click="pf.deleteDeliveryOption($index)"><i class="icon-remove"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <button type="button" class="btn btn-default" ng-click="pf.addDeliveryOption(field_name)" translate="Add delivery option"></button>
                </div>
            </div>
        </div>
    </div>
{/literal}