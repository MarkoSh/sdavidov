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
    <div ng-controller="YOrderMarketConfigureFormController as order_market">
        <panel class="order-market-form" no-collapse="true" heading="Order on Yandex.Market">
            <form-group label="Shop URL">
                {/literal}
                {assign var="api_url" value=$link->getModuleLink('seosayandexservices', 'front', ['component' => 'ordermarket', 'component_controller' => 'api', 'query' => ''], true)}
                <a target="_blank" href="{$api_url|escape:'quotes':'UTF-8'}">{$api_url|escape:'quotes':'UTF-8'}</a>
                {literal}
            </form-group>
            <form-group label="Yandex API url">
                <input ng-change="configure.saveConfigurationValueDelayed('OM_API_URL', order_market.config.OM_API_URL)" type="text" ng-model="order_market.config.OM_API_URL">
            </form-group>
            <form-group label="Yandex App id">
                <input ng-change="configure.saveConfigurationValueDelayed('OM_APP_ID', order_market.config.OM_APP_ID)" type="text" ng-model="order_market.config.OM_APP_ID">
                <a target="_blank" href="https://oauth.yandex.ru/" translate="Get app id"></a>
            </form-group>
            <form-group label="Yandex token">
                <input ng-change="configure.saveConfigurationValueDelayed('OM_TOKEN', order_market.config.OM_TOKEN)" type="text" ng-model="order_market.config.OM_TOKEN">
                <a ng-if="order_market.config.OM_APP_ID" target="_blank" href="https://oauth.yandex.ru/authorize?response_type=token&client_id={{order_market.config.OM_APP_ID}}" translate="Get token"></a>
                <div ng-if="!order_market.config.OM_APP_ID" class="alert alert-danger" translate="Please, set App id"></div>
            </form-group>
            <form-group label="Campaign id">
                <input ng-change="configure.saveConfigurationValueDelayed('OM_CAMPAIGN_ID', order_market.config.OM_CAMPAIGN_ID)" type="text" ng-model="order_market.config.OM_CAMPAIGN_ID">
                <img ng-src="{{order_market.config.img_url}}help.jpg">
            </form-group>
            <form-group label="Sha1">
                <input ng-change="configure.saveConfigurationValueDelayed('OM_SHA1', order_market.config.OM_SHA1)" type="text" ng-model="order_market.config.OM_SHA1">
                <a href="https://partner.market.yandex.ru/api-settings.xml?id=" target="_blank" translate="Get sha1"></a>
            </form-group>
            <form-group label="Payment by credit card upon receipt of order">
                <prestashop-switch id="CAR
                D_ON_DELIVERY" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('CARD_ON_DELIVERY', order_market.config.CARD_ON_DELIVERY)" ng-model="order_market.config.CARD_ON_DELIVERY"></prestashop-switch>
            </form-group>
            <form-group label="Cash on delivery order">
                <prestashop-switch id="CASH_ON_DELIVERY" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('CASH_ON_DELIVERY', order_market.config.CASH_ON_DELIVERY)" ng-model="order_market.config.CASH_ON_DELIVERY"></prestashop-switch>
            </form-group>
            <form-group label="Enable change delivery">
                <prestashop-switch id="OM_ENABLE_CHANGE_DELIVERY" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('OM_ENABLE_CHANGE_DELIVERY', order_market.config.OM_ENABLE_CHANGE_DELIVERY)" ng-model="order_market.config.OM_ENABLE_CHANGE_DELIVERY"></prestashop-switch>
            </form-group>
            <form-group label="Carriers">
                <table class="table">
                    <thead>
                        <th translate="ID"></th>
                        <th translate="Name"></th>
                        <th translate="Select type delivery"></th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="carrier in order_market.carriers">
                            <td>
                                {{carrier.id_carrier}}
                            </td>
                            <td>{{carrier.name}}</td>
                            <td>
                                <select ng-change="configure.saveConfigurationValueDelayed('CARRIERS', order_market.config.CARRIERS)" ng-model="order_market.config.CARRIERS[carrier.id_reference]">
                                    <option value="DELIVERY" translate="DELIVERY"></option>
                                    <option value="PICKUP" translate="PICKUP"></option>
                                    <option value="POST" translate="POST"></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form-group>
        </panel>
    </div>
{/literal}