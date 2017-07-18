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
<ul class="nav nav-pills">
    <li {literal}ng-class="{active: configure.router.isCurrentState('welcome')}"{/literal}>
        <a {literal}ng-click="configure.router.setState('welcome', {}, $event)"{/literal} href="#" translate="Homepage"></a>
    </li>
    {foreach $components as $component}
        {if $component->hasNavigationEntry()}
            {assign var="entry" value=$component->getNavigationEntry()}
            <li ng-class="{
                active: configure.router.isCurrentState('{$entry.route|escape:'htmlall':'UTF-8'}')
            }">
                <a ng-click="configure.router.setState('{$entry.route|escape:'htmlall':'UTF-8'}', null, $event)"
                   href="#" translate="{$entry.name|escape:'htmlall':'UTF-8'}"></a>
            </li>
        {/if}
    {/foreach}
    <li {literal}ng-class="{active: configure.router.isCurrentState('documentation')}"{/literal}>
        <a {literal}ng-click="configure.router.setState('documentation', {}, $event)"{/literal} href="#" translate="Documentation"></a>
    </li>
    <li {literal}ng-class="{active: configure.router.isCurrentState('our modules')}"{/literal}>
        <a href="#" id="seosa_manager_btn" translate="Our modules"></a>
    </li>
</ul>

<script src='https://seosaps.com/ru/module/seosamanager/manager?ajax=1&action=script&iso_code={Context::getContext()->language->iso_code|escape:'quotes':'UTF-8'}'></script>