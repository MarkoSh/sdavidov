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
        <label translate="Products"></label>
        <ul class="nav nav-pills">
            <li ng-class="{active: ngModel.$viewValue.mode === 'all'}">
                <a ng-click="setMode('all', $event)" href="#" translate="All"></a>
            </li>
            <li ng-class="{active: ngModel.$viewValue.mode === 'active'}">
                <a ng-click="setMode('active', $event)" href="#" translate="Active"></a>
            </li>
            <li ng-class="{active: ngModel.$viewValue.mode === 'not_active'}">
                <a ng-click="setMode('not_active', $event)" href="#" translate="Disabled"></a>
            </li>
            <li ng-class="{active: ngModel.$viewValue.mode === 'by_category'}">
                <a ng-click="setMode('by_category', $event)" href="#" translate="By category"></a>
            </li>
            <li ng-class="{active: ngModel.$viewValue.mode === 'by_manufacturer'}">
                <a ng-click="setMode('by_manufacturer', $event)" href="#" translate="By manufacturer"></a>
            </li>
            <li ng-class="{active: ngModel.$viewValue.mode === 'by_supplier'}">
                <a ng-click="setMode('by_supplier', $event)" href="#" translate="By supplier"></a>
            </li>
            <li  ng-class="{active: ngModel.$viewValue.mode === 'selected'}">
                <a ng-click="setMode('selected', $event)" href="#" translate="Chosen"></a>
            </li>
            <li ng-class="{active: ngModel.$viewValue.mode === 'not_selected'}">
                <a ng-click="setMode('not_selected', $event)" href="#" translate="Not chosen"></a>
            </li>
        </ul>
    </div>
    <div ng-show="needSelector( ngModel.$viewValue.mode)">
        <div class="row search-row">
            <div class="col-md-4 col-lg-2">
                <label for="{{ directiveId }}_product_filter_search" translate="Search"></label>
            </div>
            <div class="col-md-8 col-lg-10">
                <div class="input-group">
                    <input ng-model="settings.searchQuery" id="{{ directiveId }}_product_filter_search" type="text" value="" autocomplete="off" class="ac_input">
                    <span class="input-group-addon"><i class="icon-search"></i></span>
                </div>
            </div>
        </div>
        <div class="row selects-row">
            <div class="col-md-6">
                <label for="{{ directiveId }}_product_filter_not_selected" translate="Not selected"></label>
                <select ng-dblclick="addItems()" ng-model="tmpNotSelected" id="{{ directiveId }}_product_filter_not_selected" multiple>
                    <option ng-repeat="item in items"
                            ng-value="$index"
                            ng-if="isNotSelected(item) && matchSearch(item.id + ' | ' + item.name, settings.searchQuery)"
                            ng-bind="item.id + ' | ' + item.name"></option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="{{ directiveId }}_product_filter_selected" translate="Selected"></label>
                <select ng-dblclick="removeItems()" ng-model="tmpSelected" id="{{ directiveId }}_product_filter_selected" multiple>
                    <option ng-repeat="item in items" ng-if="!isNotSelected(item)"
                            ng-value="$index"
                            ng-bind="item.id + ' | ' + item.name"></option>
                </select>
            </div>
        </div>
        <div class="row buttons-row">
            <div class="col-md-6">
                <button type="button" ng-click="addItems()" class="btn button btn-default">
                    <span translate="Add"></span>
                    <i class="icon-arrow-right hide-ps15"></i>
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" ng-click="removeItems()" class="btn button btn-default">
                    <i class="icon-arrow-left hide-ps15"></i>
                    <span translate="Remove"></span>
                </button>
            </div>
        </div>
    </div>
{/literal}