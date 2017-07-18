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

<div lazy-load="'sya.'+component.name" class="component-form">
    <div class="row">
        <div class="col-md-3">
            <panel ng-if="!!component.has_welcome" no-collapse>
                <div>
                    <component-welcome component="component"></component-welcome>
                    <hr>
                </div>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <form-group label="Enabled">
                            <prestashop-switch
                                    ng-model="component.enabled"
                                    ng-change="configure.setComponentStatus(
                                       component.name, component.enabled
                                    )" ></prestashop-switch>
                        </form-group>
                    </div>
                </div>
            </panel>

            <div ng-if="!!component.enabled" ng-include="leftFormTemplatePath"></div>
        </div>
        <div class="col-md-9">
            <div ng-if="!!component.enabled" ng-include="formTemplatePath"></div>
            <div ng-if="!component.enabled">
                <panel no-collapse>
                    <h2 class="text-center" translate="Module is disabled"></h2>
                </panel>
            </div>
        </div>
    </div>
</div>
