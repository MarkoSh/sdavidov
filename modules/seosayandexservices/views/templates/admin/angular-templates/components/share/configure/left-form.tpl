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
    <div ng-controller="YShareConfigureFormController as share">
        <panel heading="Display">
            <form-group label="On product pages">
                <prestashop-switch ng-model="share.config.display_on_product_page"
                                   ng-change="configure.saveConfigurationValue('share_display_on_product_page', share.config.display_on_product_page)"></prestashop-switch>
            </form-group>
            <form-group label="On compare page">
                <prestashop-switch ng-model="share.config.display_on_compare_page"
                                   ng-change="configure.saveConfigurationValue('share_display_on_compare_page', share.config.display_on_compare_page)"></prestashop-switch>
            </form-group>
            <form-group label="On CMS pages">
                <prestashop-switch ng-model="share.config.display_on_cms_page"
                                   ng-change="configure.saveConfigurationValue('share_display_on_cms_page', share.config.display_on_cms_page)"></prestashop-switch>
            </form-group>
        </panel>
    </div>
{/literal}