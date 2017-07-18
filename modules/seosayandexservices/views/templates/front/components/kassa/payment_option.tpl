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

<form action="{$url|escape:'quotes':'UTF-8'}" method="post">
    <input name="shopId" value="{$shop_id|escape:'quotes':'UTF-8'}" type="hidden"/>
    <input name="scid" value="{$scid|escape:'quotes':'UTF-8'}" type="hidden"/>
    <input name="sum" value="{$total_order|floatval}" type="hidden">
    <input name="customerNumber" value="{$customer_number|escape:'quotes':'UTF-8'}" type="hidden"/>
    <input type="hidden" name="orderNumber" value="{$order_number|intval}">
    <input name="paymentType" value="{$type|escape:'quotes':'UTF-8'}" type="hidden"/>
    <input name="cms_name" value="prestashop-seosa" type="hidden"/>
    <input name="cps_email" value="{$customer_email|escape:'quotes':'UTF-8'}" type="hidden"/>
    <input name="cps_phone" value="{$customer_phone|escape:'quotes':'UTF-8'}" type="hidden"/>
</form>