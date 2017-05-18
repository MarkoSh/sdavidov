{*

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

<link href="{$this_path|escape:'htmlall':'UTF-8'}views/css/skrill.css" rel="stylesheet" type="text/css">

{foreach from=$payments key=sort item=payment}
	{assign var='payment_id' value=$payment.name|ucfirst}
	{assign var='payment_link' value="payment`$payment_id`"}
	<div class="row">
		<div class="col-xs-12">
			<p class="payment_module">
				<a href="{$link->getModuleLink('skrill', $payment_link, [], true)|escape:'htmlall':'UTF-8'}" id="skrill-{$payment.name|escape:'htmlall':'UTF-8'}" onmouseover="this.style.textDecoration='none';">
					{if $payment.logos }
						{foreach from=$payment.logos key=i item=logo}
							<img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/{$logo|escape:'htmlall':'UTF-8'}" alt="{$payment.label|escape:'htmlall':'UTF-8'}" height="28"/>
						{/foreach}
						{$payment.label|escape:'htmlall':'UTF-8'}
					{else}
						<img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/{$payment.name|escape:'htmlall':'UTF-8'}.png" alt="{$payment.label|escape:'htmlall':'UTF-8'}" height="49"/>
					{/if}
				</a>
			</p>
		</div>
	</div>
{/foreach}
