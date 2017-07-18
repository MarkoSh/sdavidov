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

<h1 class="module-name text-center">
    <span>{$module->displayName|escape:'htmlall':'UTF-8'}</span>
    <span>v<span>{$module->version|escape:'htmlall':'UTF-8'}</span></span>
</h1>
<hr>
{literal}
<div class="text-center">
    <ul class="list-inline">
        <li ng-repeat="component in configure.components"
            ng-if="component.has_welcome"
            ng-class="{'has-form': component.has_config_form}"
            class="component"    >
            <component-welcome component="component"></component-welcome>
        </li>
    </ul>
</div>
{/literal}