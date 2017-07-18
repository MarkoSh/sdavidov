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
    <div ng-controller="YPartnerConfigureFormController as partner">
        <panel class="yandex-metrika-form" no-collapse="true" heading="Yandex Partner">
            <div class="row" ng-repeat="hook_group in partner.hooks">
                <div ng-class="'col-md-'+(12 / hook_group.length)" ng-repeat="hook in hook_group">
                    <div class="form-group">
                        <label ng-bind="hook.label"></label>
                        <textarea ng-model="partner.config[hook.name]"
                               type="text"
                               class="form-control"
                               ng-change="configure.saveConfigurationValueDelayed('partner_'+hook.name, partner.config[hook.name], 500, false, true)"
                        ></textarea>
                    </div>

                </div>
            </div>
        </panel>
    </div>
{/literal}