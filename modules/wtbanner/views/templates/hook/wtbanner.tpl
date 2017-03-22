{*
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

{if isset($banners)}
<!-- MODULE Block banner -->
<div class="wt-block-banner">
	<div class="container{if $banner_config->used_slider == 0} no-slider{/if}">
		<div class="row{if $banner_config->used_slider == 0} wt-{$banner_config->number_banner_aline|escape:'html':'UTF-8'}-items-aline{/if}">
			<ul id="wt_banner_content" class="container-list banner-content">
			{foreach from=$banners item=banner name=banners}
				<li class="item">
					<div class="banner-content">
					<a href="{$banner.link|escape:'html':'UTF-8'}" title=""><img src="{$module_dir|escape:'html':'UTF-8'}views/img/{$banner.file_name|escape:'html':'UTF-8'}" alt="" /></a>
						{if isset($banner.text)}{$banner.text|escape:'quotes':'UTF-8'}{/if}
					</div>
				</li>
			{/foreach}
			</ul>
		</div>
	</div>
</div>
{if isset($banner_config->used_slider) && $banner_config->used_slider == 1}
<script type="text/javascript">
	$(document).ready(function(){
		var owl_manu = $("#wt_banner_content");
		imagesLoaded(owl_manu, function() {
		$("#wt_banner_content").owlCarousel({
		  loop: true,
			responsive: {
				0: { items: 1},
				464:{ items: 1},
				750:{ items: 3},
				974:{ items: 3},
				1170:{ items: 3}
			},
		  dots: false,
		  nav: true
		  });
		});
	});
</script>
{/if}
<!-- /MODULE Block banner -->
{/if}