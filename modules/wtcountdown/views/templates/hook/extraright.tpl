{**
* 2007-2015 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{assign var='specific_prices' value=$specific_prices}
{if ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' < $specific_prices.to )&& ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from)&& ($specific_prices.to != '0000-00-00 00:00:00')}
	<div class="wt-count-down">
		<div id="countdown_{$id_product|intval}" class="timer">
			<span class="countdown-title">{l s='Remaining Time' mod='wtcountdown'} :</span>
			<div class="wt_countdown" data-date="{$specific_prices.to|date_format:'%Y/%m/%d %H:%M:%S'|escape:'html':'UTF-8'}">
				<ul class="clock">
					<li><span data-days class="days">0</span><p class="timeRefDays">{l s='days' mod='wtcountdown'}</p></li>
					<li>
						<ul class="clock-sub">
						<li><span data-hours class="hour">0</span><p class="timeRefhour">{l s='hours' mod='wtcountdown'}</p></li>
						<li><span data-minutes class="minutes">0</span><p class="timeRefminutes">{l s='mins' mod='wtcountdown'}</p></li>
						<li><span data-seconds class="remainingSeconds">0</span><p class="timeRefseconds">{l s='Secs' mod='wtcountdown'}</p></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.wt_countdown').countdown({
					autoStart: true,
				});
			});
		</script>
	</div>
	{elseif ($specific_prices.to == '0000-00-00 00:00:00') && ($specific_prices.from == '0000-00-00 00:00:00')}
		<div class="wt-count-down hidden">
			 <b>{l s='Limited Special Offer' mod='wtcountdown'}</b>
		</div>
	{/if}