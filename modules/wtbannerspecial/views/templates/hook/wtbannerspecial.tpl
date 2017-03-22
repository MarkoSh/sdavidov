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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="wt-banner-special">
	<div class="banner-special-fixed">
		<a class="close-banner-special" href="javascript: void(0)"><span>{l s='close' mod='wtbannerspecial'}</span></a>
		<div class="banner-special-content">
			<a href="{if $banner_link|escape:'htmlall':'UTF-8'}{$banner_link|escape:'htmlall':'UTF-8'}{else}{if isset($force_ssl) && $force_ssl}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}{/if}" title="{$banner_desc|escape:'htmlall':'UTF-8'}">
			{if isset($banner_img)}
				<img class="img-responsive" src="{$banner_img|escape:'htmlall':'UTF-8'}" alt="" title=""/>
			{/if}
			{if isset($banner_desc) && $banner_desc != ''}
				{$banner_desc|escape:'quotes':'UTF-8'}
			{/if}
			</a>
		</div>
	</div>
	<a class="show-banner-special" href="javascript: void(0)"><span>{l s='Show' mod='wtbannerspecial'}</span></a>
	<script type="text/javascript">
		var cookies_time = {$cookies_time|intval};
	</script>
</div>
