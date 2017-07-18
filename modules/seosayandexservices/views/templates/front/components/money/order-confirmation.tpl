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

<p class="alert alert-success">{l s='Your order on %s is complete.' sprintf=$shop_name mod='seosayandexservices'}</p>
<div class="box">
    {l s='Please send us a bank wire with' mod='seosayandexservices'}
    <br />- {l s='Amount' mod='seosayandexservices'} <span class="price"><strong>{$total_to_pay|escape:'quotes':'UTF-8'}</strong></span>
    <br />{l s='An email has been sent with this information.' mod='seosayandexservices'}
    <br /> <strong>{l s='Your order will be sent as soon as we receive payment.' mod='seosayandexservices'}</strong>
    <br />{l s='If you have questions, comments or concerns, please contact our' mod='seosayandexservices'}<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team' mod='seosayandexservices'}</a>.
</div>
