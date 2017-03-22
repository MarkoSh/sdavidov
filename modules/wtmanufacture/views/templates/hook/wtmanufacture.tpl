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
{$number_line = 1}
{if isset($manufacs)}
<!-- MODULE Block manufacture -->
<div class="wt-block-manu clearfix">
	<div class="out-manu{if $cs_config->used_slider == 0} no-slider{/if}">
		<div class="manu-content cleanfix">
			<div class="banner-left wt-col-md-2">
				<div id="wt_manu_left" class="container-list manu-content">
				{foreach from=$banners_l item=banner name=banners}
					<div class="item">
						<div class="banner-content">
						<a href="{$banner.link|escape:'html':'UTF-8'}" title=""><img src="{$module_dir|escape:'html':'UTF-8'}views/img/{$banner.file_name|escape:'html':'UTF-8'}" alt="" /></a>
							{if isset($banner.text)}{$banner.text|escape:'quotes':'UTF-8'}{/if}
						</div>
					</div>
				{/foreach}
				</div>
			</div>
			
			<div class="list_manu col-xs-12 col-sm-12">
				<div id="ul_manu">
				{foreach from=$manufacs item=manufacturer name=manufacturer_list}
					{if $smarty.foreach.manufacturer_list.iteration % $number_line == 1 || $number_line == 1}
					<div class="{if $smarty.foreach.manufacturer_list.first}first_item{elseif $smarty.foreach.manufacturer_list.last}last_item{/if}">
					{/if}
						{if file_exists($ps_manu_img_dir|cat:$manufacturer.id_manufacturer|cat:'.jpg')}
							<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
							<img src="{$img_manu_dir|escape:'htmlall':'UTF-8'}{$manufacturer.id_manufacturer|escape:'htmlall':'UTF-8'}-medium_default.jpg" alt="{$manufacturer.name|escape:'htmlall':'UTF-8'}" /></a>
						{/if}
					{if $smarty.foreach.manufacturer_list.iteration % $number_line == 0 || $smarty.foreach.manufacturer_list.last || $number_line == 1}
					</div>
					{/if}
				{/foreach}
				</div>
			</div>
			<div class="banner-right wt-col-md-2">
				<div id="wt_manu_right" class="container-list manu-content">
				{foreach from=$banners_r item=banner name=banners}
					<div class="item">
						<div class="banner-content">
						<a href="{$banner.link|escape:'UTF-8'}" title=""><img src="{$module_dir|escape:'html':'UTF-8'}views/img/{$banner.file_name|escape:'html':'UTF-8'}" alt="" /></a>
							{if isset($banner.text)}{$banner.text|escape:'quotes':'UTF-8'}{/if}
						</div>
					</div>
				{/foreach}
				</div>
			</div>
		</div>
	</div>
</div>
{if $cs_config->used_slider == 1}
<script type="text/javascript">
	$(window).load(function(){
	var owl_manu = $("#ul_manu");
	imagesLoaded(owl_manu, function() {
		$("#ul_manu").owlCarousel({
		  loop: true,
		responsive: {
			0: { items: 2},
			464:{ items: 2},
			750:{ items: 4},
			974:{ items: 6},
			1170:{ items: 7}
		},
		  dots: false,
		  nav: true
		  });
		});
	});
</script>
{/if}
<!-- /MODULE Block manufacture -->
{/if}