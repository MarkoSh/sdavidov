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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !$is_17}{include file="$tpl_dir./errors.tpl"}{/if}

{capture name=path}
    <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}"
       title="{l s='Go back to the Checkout' mod='seosayandexservices'}">{l s='Checkout' mod='seosayandexservices'}</a>
    <span class="navigation-pipe">{$navigationPipe|escape:'quotes':'UTF-8'}</span>
    {if $type === 'wallet'}
    {l s='Yandex.Money Wallet payment' mod='seosayandexservices'}
    {else}
    {l s='Yandex.Money Bank card' mod='seosayandexservices'}
    {/if}
{/capture}

<h1 class="page-heading">
    {l s='Order summary' mod='seosayandexservices'}
</h1>

{if !$is_17}
    {assign var='current_step' value='payment'}
    {include file="$tpl_dir./order-steps.tpl"}
{/if}

{if $products_count <= 0}
    <p class="alert alert-warning">
        {l s='Your shopping cart is empty.' mod='seosayandexservices'}
    </p>
{else}
    <form action="{$payment_url|escape:'html':'UTF-8'}" method="post">
        <div class="box cheque-box">
            <h3 class="page-subheading">
                {if $type === 'wallet'}
                    {l s='Yandex.Money Wallet payment' mod='seosayandexservices'}
                {else}
                    {l s='Yandex.Money Bank card payment' mod='seosayandexservices'}
                {/if}
            </h3>
            <p class="cheque-indent">
                <strong class="dark">
                    {if $type === 'wallet'}
                        {l s='You have chosen to pay from Yandex.Money wallet.' mod='seosayandexservices'} {l s='Here is a short summary of your order:' mod='seosayandexservices'}
                    {else}
                        {l s='You have chosen to pay from Yandex.Money bank card.' mod='seosayandexservices'} {l s='Here is a short summary of your order:' mod='seosayandexservices'}
                    {/if}
                </strong>
            </p>
            <p>
                - {l s='The total amount of your order is' mod='seosayandexservices'}
                <span id="amount" class="price">{displayPrice price=$order_total}</span>
                {if $use_taxes == 1}
                    {l s='(tax incl.)' mod='seosayandexservices'}
                {/if}
            </p>
            <p>
                - {l s='Please confirm your order by clicking "I confirm my order".' mod='seosayandexservices'}
            </p>
        </div>
        <p class="cart_navigation clearfix" id="cart_navigation">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
                <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='seosayandexservices'}
            </a>
            <button class="button btn btn-default button-medium" type="submit">
                <span>{l s='I confirm my order' mod='seosayandexservices'}<i class="icon-chevron-right right"></i></span>
            </button>
        </p>
    </form>
{/if}
