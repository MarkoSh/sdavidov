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

<div ng-app="sya.configure" ng-strict-di="true" id="seosayandexservices" class="seosayandexservices custom_bootstrap">
    {include "./_angular_translations.tpl"}
    {include "./_angular_templates.tpl"}
    {include "./_angular_variables.tpl"}
    <div ng-controller="ConfigureController as configure">
        <div ng-show="!configure.router.isCurrentState('welcome')" {if SYATools::isPsGreater('1.6.0')}class="panel"{/if}>
            <nav header-navigation></nav>
            {if SYATools::isPsLower('1.6.1')}
                <hr>
            {/if}
        </div>

        <welcome-window ng-show="configure.router.isCurrentState('welcome')"></welcome-window>
        <documentation-window ng-show="configure.router.isCurrentState('documentation')"></documentation-window>

        <component-configure-form
                ng-repeat="component in configure.components"
                ng-if="component.has_config_form && configure.router.isCurrentState('configure-'+component.name)"
                component="component"></component-configure-form>
    </div>
</div>

