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
    <li ng-repeat="(path, item) in tree"
        ng-class="{
            current: isCurrentCategory(path),
            active: isCurrentPage(path)
        }"
        >
        <a ng-if="!isPage(path)"
           ng-click="toggleNested($event)"
           ng-bind="displayPath(path)">
        </a>
        <a ng-if="isPage(path)"
           book-link="{{ (parent ? parent+ '/' : '') + path }}"
           ng-bind="displayPath(path)"></a>

        <ul ng-if="!isPage(path)" table-of-contents="item" parent="(parent ? parent+ '/' : '') + path"></ul>
    </li>
{/literal}