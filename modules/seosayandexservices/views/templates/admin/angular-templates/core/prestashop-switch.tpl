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
<span ng-class="{disabled: disabled}" class="switch prestashop-switch">
    <input ng-disabled="disabled" ng-value="trueValue" ng-model="tmpValue" ng-change="sync()" type="radio" name="{{ name }}" id="{{ id }}_on">
    <label for="{{ id }}_on" ng-bind="onText | translate"></label>

    <input ng-disabled="disabled" ng-value="falseValue" ng-model="tmpValue" ng-change="sync()" type="radio" name="{{ name }}" id="{{ id }}_off">
    <label for="{{ id }}_off" ng-bind="offText | translate"></label>
    <a class="slide-button btn"></a>
</span>
{/literal}