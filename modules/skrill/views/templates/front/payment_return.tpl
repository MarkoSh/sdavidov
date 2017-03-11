{**
* 2015 Skrill
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
*  @author Skrill <contact@skrill.com>
*  @copyright  2015 Skrill
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{block name="content"}
<section>
    <p>
        {if {$skrillErrorMessage == ""}}
        {if {l s='FRONTEND_MESSAGE_YOUR_ORDER' mod='skrill'} == "FRONTEND_MESSAGE_YOUR_ORDER"}Your order on{else}{l s='FRONTEND_MESSAGE_YOUR_ORDER' mod='skrill'}{/if} {$shop_name|escape:'htmlall':'UTF-8'} {if {l s='FRONTEND_MESSAGE_INPROCESS' mod='skrill'} == "FRONTEND_MESSAGE_INPROCESS"}is in process.{else}{l s='FRONTEND_MESSAGE_INPROCESS' mod='skrill'}{/if}<br/>
        {if {l s='FRONTEND_MESSAGE_PLEASE_BACK_AGAIN' mod='skrill'} == "FRONTEND_MESSAGE_PLEASE_BACK_AGAIN"}Please back again after a minutes and check your order histoy{else}{l s='FRONTEND_MESSAGE_PLEASE_BACK_AGAIN' mod='skrill'}{/if}
        {else}
	    <div class="alert alert-danger">
	        <button type="button" class="close" data-dismiss="alert">×</button>
	        {$skrillErrorMessage|escape:'html':'UTF-8'}
	    </div>
        {/if}
    </p>
</section>
{/block}
