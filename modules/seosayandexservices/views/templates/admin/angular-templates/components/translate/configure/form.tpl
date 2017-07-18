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
    <div ng-controller="YTranslateConfigureFormController as translate">
        <panel class="yandex-site-form" no-collapse="true" heading="Yandex Translate">
            <form-group label="Api key">
                <input ng-model="translate.config.api_key"
                       type="text"
                       class="form-control"
                       ng-change="configure.saveConfigurationValueDelayed('translate_api_key', translate.config.api_key, 500)"
                />
            </form-group>
            <div>
                <label translate="Front end translation mode"></label>
                <select ng-options="mode as label for (mode, label) in translate.modes"
                        ng-model="translate.config.front_end_translation_mode"
                       type="text"
                       class="form-control"
                           ng-change="configure.saveConfigurationValue(
                           'translate_front_end_translate_mode',
                           translate.config.front_end_translation_mode
                       )"
                ></select>
            </div>
        </panel>
    </div>
{/literal}