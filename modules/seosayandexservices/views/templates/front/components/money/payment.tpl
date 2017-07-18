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

{if SYAMoney::isWalletEnabled()}
<div class="row">
    <div class="col-xs-12">
        <form method="post" action="{$syamoney_wallet_sp_url|escape:'html':'UTF-8'}" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="client_id" value="{$syamoney_app_id|escape:'html':'UTF-8'}" />
            <input type="hidden" name="response_type" value="code" />
            <input type="hidden" name="redirect_uri"  value="{$syamoney_wallet_redirect_url|escape:'html':'UTF-8'}" />
            <input type="hidden" name="scope" value='{$syamoney_wallet_scope|escape:'html':'UTF-8'}' />

            <p class="payment_module">
                <a class='yandex-money-payment yandex-money-wallet' onclick="$(this).closest('form').submit()"
                   title="{l s='Yandex.Money Wallet' mod='seosayandexservices'}">
                    {l s='Yandex.Money Wallet' mod='seosayandexservices'}
                </a>
            </p>
        </form>
    </div>
</div>
{/if}
{if SYAMoney::isCardEnabled() }
<div class="row">
    <div class="col-xs-12">
        <form method="post" action="{$syamoney_card_sp_url|escape:'html':'UTF-8'}" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="client_id" value="{$syamoney_app_id|escape:'html':'UTF-8'}" />
            <input type="hidden" name="response_type" value="code" />
            <input type="hidden" name="redirect_uri"  value="{$syamoney_card_redirect_url|escape:'html':'UTF-8'}" />
            <input type="hidden" name="scope" value='{$syamoney_card_scope|escape:'html':'UTF-8'}' />

            <p class="payment_module">
                <a class='yandex-money-payment yandex-money-card' onclick="$(this).closest('form').submit()"
                   title="{l s='Yandex.Money Bank Card' mod='seosayandexservices'}">
                    {l s='Yandex.Money Bank Card' mod='seosayandexservices'}
                </a>
            </p>
        </form>
    </div>
</div>
{/if}
