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
    <div ng-controller="YMetrikaConfigureFormController as metrika">
        <panel class="yandex-metrika-form" no-collapse="true" heading="Yandex Metrika">
            <label translate="Counter"></label>
            <textarea ng-model="metrika.config.counter"
                      class="form-control"
                      ng-change="configure.saveConfigurationValueDelayed('metrika_counter', metrika.config.counter, 500, true)"
                    ></textarea>
        </panel>

        <panel heading="Goals">
           <table class="table">
               <thead>
               <tr>
                   <td translate="Event"></td>
                   <td translate="Description"></td>
               </tr>
               </thead>
               <tbody>
               <tr ng-repeat="goal in metrika.goals">
                   <td>
                       <input type="text" class="form-control" ng-value="goal.event" readonly="readonly">
                   </td>
                   <td ng-bind="goal.description | translate"></td>
                   <td>
               </tr>
               </tbody>
           </table>
        </panel>
    </div>
{/literal}