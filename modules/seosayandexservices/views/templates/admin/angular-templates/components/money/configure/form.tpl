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
    <div ng-controller="YMoneyConfigureFormController as money">
        <panel class="yandex-money-form" no-collapse="true" heading="Yandex Money">
            <form-group label="Yandex Money Application ID">
                <input ng-model="money.config.app_id"
                       type="text"
                       class="form-control"
                       ng-change="configure.saveConfigurationValueDelayed('money_app_id', money.config.app_id, 500)"
                />
            </form-group>
            <form-group label="Secret key">
                <textarea ng-model="money.config.secret_key"
                       type="text"
                       class="form-control"
                       ng-change="configure.saveConfigurationValueDelayed('money_secret_key', money.config.secret_key, 500)"
                ></textarea>
            </form-group>
            <form-group label="Target wallet">
                <input ng-model="money.config.target_wallet"
                       type="text"
                       class="form-control"
                       ng-change="configure.saveConfigurationValueDelayed('money_target_wallet', money.config.target_wallet, 500)"
                />
            </form-group>
            <div class="row">
                <div class="col-md-6">
                    <form-group label="Enable wallet payment">
                        <prestashop-switch ng-model="money.config.enable_wallet"
                                           ng-change="configure.saveConfigurationValue('money_enable_wallet', money.config.enable_wallet)"></prestashop-switch>
                    </form-group>
                </div>
                <div class="col-md-6">
                    <form-group label="Enable card payment">
                        <prestashop-switch ng-model="money.config.enable_card"
                                           ng-change="configure.saveConfigurationValue('money_enable_card', money.config.enable_card)"></prestashop-switch>
                    </form-group>
                </div>
            </div>
            <div >
                <label translate="Redirect URL"></label>
                <input type="text" ng-value="money.redirect_url" readonly="readonly">
            </div>
        </panel>
    </div>
{/literal}