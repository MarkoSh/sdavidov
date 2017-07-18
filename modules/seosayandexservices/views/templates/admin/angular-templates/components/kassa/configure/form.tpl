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
    <div ng-controller="YKassaConfigureFormController as kassa">
        <panel class="yandex-kassa-form" no-collapse="true" heading="Yandex Kassa">
            <form-group label="Demo mode?">
                <prestashop-switch id="demo_mode" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('demo_mode', kassa.config.demo_mode)" ng-model="kassa.config.demo_mode"></prestashop-switch>
            </form-group>
            <form-group label="Shop ID">
                <input ng-change="configure.saveConfigurationValueDelayed('shop_id', kassa.config.shop_id)" type="text" ng-model="kassa.config.shop_id">
            </form-group>
            <form-group label="SCID">
                <input ng-change="configure.saveConfigurationValueDelayed('scid', kassa.config.scid)" type="text" ng-model="kassa.config.scid">
            </form-group>
            <form-group label="Secret word">
                <input maxlength="20" ng-change="configure.saveConfigurationValueDelayed('shop_password', kassa.config.shop_password)" type="text" ng-model="kassa.config.shop_password">
            </form-group>
            <form-group label="Check order URL">
                {/literal}
                    {assign var="check_url" value=$link->getModuleLink('seosayandexservices', 'front', ['component' => 'kassa', 'component_controller' => 'check_order'])}
                    <a target="_blank" href="{$check_url|escape:'quotes':'UTF-8'}">{$check_url|escape:'quotes':'UTF-8'}</a>
                {literal}
            </form-group>
            <form-group label="Payment aviso URL">
                {/literal}
                    {assign var="payment_aviso" value=$link->getModuleLink('seosayandexservices', 'front', ['component' => 'kassa', 'component_controller' => 'payment_aviso'])}
                    <a target="_blank" href="{$payment_aviso|escape:'quotes':'UTF-8'}">{$payment_aviso|escape:'quotes':'UTF-8'}</a>
                {literal}
            </form-group>
            <form-group label="Success URL">
                {/literal}
                    {assign var="success" value=$link->getModuleLink('seosayandexservices', 'front', ['component' => 'kassa', 'component_controller' => 'success_payment'])}
                    <a target="_blank" href="{$success|escape:'quotes':'UTF-8'}">{$success|escape:'quotes':'UTF-8'}</a>
                {literal}
            </form-group>
            <form-group label="Fail URL">
                {/literal}
                    {assign var="fail" value=$link->getModuleLink('seosayandexservices', 'front', ['component' => 'kassa', 'component_controller' => 'fail_payment'])}
                    <a target="_blank" href="{$fail|escape:'quotes':'UTF-8'}">{$fail|escape:'quotes':'UTF-8'}</a>
                {literal}
            </form-group>
            <form-group label="Payment from a purse in Yandex.Money">
                <prestashop-switch id="payment_pc" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_pc', kassa.config.payment_pc)" ng-model="kassa.config.payment_pc"></prestashop-switch>
            </form-group>
            <form-group label="Payment of any credit card.">
                <prestashop-switch id="payment_ac" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_ac', kassa.config.payment_ac)" ng-model="kassa.config.payment_ac"></prestashop-switch>
            </form-group>
            <form-group label="Payment by mobile phone account.">
                <prestashop-switch id="payment_mc" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_mc', kassa.config.payment_mc)" ng-model="kassa.config.payment_mc"></prestashop-switch>
            </form-group>
            <form-group label="Cash and cash through the terminal.">
                <prestashop-switch id="payment_gp" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_gp', kassa.config.payment_gp)" ng-model="kassa.config.payment_gp"></prestashop-switch>
            </form-group>
            <form-group label="Payment from a purse in system WebMoney.">
                <prestashop-switch id="payment_wm" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_wm', kassa.config.payment_wm)" ng-model="kassa.config.payment_wm"></prestashop-switch>
            </form-group>
            <form-group label="Payment through the Savings Bank: payment by SMS or Online Savings.">
                <prestashop-switch id="payment_sb" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_sb', kassa.config.payment_sb)" ng-model="kassa.config.payment_sb"></prestashop-switch>
            </form-group>
            <form-group label="Payment via mobile terminal (mPOS).">
                <prestashop-switch id="payment_mp" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_mp', kassa.config.payment_mp)" ng-model="kassa.config.payment_mp"></prestashop-switch>
            </form-group>
            <form-group label="Payment by Alfa-Click.">
                <prestashop-switch id="payment_ab" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_ab', kassa.config.payment_ab)" ng-model="kassa.config.payment_ab"></prestashop-switch>
            </form-group>
            <form-group label="Payment via MasterPass.">
                <prestashop-switch id="payment_ma" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_ma', kassa.config.payment_ma)" ng-model="kassa.config.payment_ma"></prestashop-switch>
            </form-group>
            <form-group label="Payment by PromSviazBank.">
                <prestashop-switch id="payment_pb" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_pb', kassa.config.payment_pb)" ng-model="kassa.config.payment_pb"></prestashop-switch>
            </form-group>
            <form-group label="Payment via QIWI Wallet.">
                <prestashop-switch id="payment_qw" true-value="1" false-value="0" ng-change="configure.saveConfigurationValueDelayed('payment_qw', kassa.config.payment_qw)" ng-model="kassa.config.payment_qw"></prestashop-switch>
            </form-group>
        </panel>
    </div>
{/literal}