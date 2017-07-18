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

<script type="text/javascript">
    $(function () {
        var id_order = {Tools::getValue('id_order')|intval};
        var statuses = {$statuses|json_encode};
        {literal}
            $.each(statuses, function (key, status) {
                $("#id_order_state option[value="+ status +"]").attr({disabled: "disabled"});
            });
            $("#id_order_state").trigger("chosen:updated");
        {/literal}
    });
</script>

{if $OM_ENABLE_CHANGE_DELIVERY}
    <form action="" method="POST">
        <div class="panel">
            <div class="panel-heading">
                {l s='Form change delivery' mod='seosayandexservices'}
            </div>
            <input type="hidden" name="id_order" value="{Tools::getValue('id_order')|intval}">
            <div class="form-group clearfix">
                <label class="control-label col-lg-4">{l s='Form change delivery' mod='seosayandexservices'}</label>
                <div class="col-lg-8">
                    <select name="new_carrier">
                        {foreach from=$carriers item=carrier}
                            <option {if Tools::isSubmit('new_carrier') && Tools::getValue('new_carrier') == $carrier.id_carrier}selected{/if} value="{$carrier.id_carrier|intval}">{$carrier.name|escape:'quotes':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group clearfix">
                <label class="control-label col-lg-4">{l s='Carrier price tax incl.' mod='seosayandexservices'}</label>
                <div class="col-lg-2">
                    <input class="form-control" type="text" name="price_incl" value="{Tools::getValue('price_incl')|escape:'quotes':'UTF-8'}">
                </div>
            </div>
            <div class="form-group clearfix">
                <label class="control-label col-lg-4">{l s='Carrier price tax excl.' mod='seosayandexservices'}</label>
                <div class="col-lg-2">
                    <input class="form-control" type="text" name="price_excl" value="{Tools::getValue('price_excl')|escape:'quotes':'UTF-8'}">
                </div>
            </div>
            <div class="form-group clearfix">
                <div class="col-lg-12">
                    <button name="updateCarrierOrderMarket" class="btn btn-success">
                        {l s='Update carrier' mod='seosayandexservices'}
                    </button>
                </div>
            </div>
        </div>
    </form>
{/if}