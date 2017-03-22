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

{$number_line = 3}
{$id_lang = Context::getContext()->language->id}
{foreach from = $product_groups item = product_group name = product_group}
	<div class="{$product_group.class|escape:'html':'UTF-8'} col-sm-4">
		<h3>{$product_group.title|escape:'html':'UTF-8'}</h3>
		<div class="owlslider-prod-filter product_list">
			{foreach from=$product_group.product_list item=product name=product_list}
				{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
				<div class="item ajax_block_product">
				{/if}
					<div class="product-container">
						<div class="left-block">
							<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$product.legend|escape:html:'UTF-8'}" /></a>
						</div>
						<div class="right-block">
							<h5 class="product-name"><a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.legend|escape:html:'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></h5>
							
							{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
								<div class="content_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
										<span itemprop="price" class="price product-price">
											{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
										</span>
										<meta itemprop="priceCurrency" content="{$currency->iso_code|escape:'html':'UTF-8'}" />
										{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
											{hook h="displayProductPriceBlock" product=$product type="old_price"}
											<span class="old-price product-price">
												{displayWtPrice p=$product.price_without_reduction}
											</span>
											{if $product.specific_prices.reduction_type == 'percentage'}
												<span class="price-percent-reduction">-{$product.specific_prices.reduction|escape:'html':'UTF-8' * 100}%</span>
											{/if}
										{/if}
										{if $PS_STOCK_MANAGEMENT && isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
											{if ($product.allow_oosp || $product.quantity > 0)}
													<link itemprop="availability" href="http://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='wtproductfilter'}{/if}{else}{l s='Out of stock' mod='wtproductfilter'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='wtproductfilter'}{/if}{/if}
											{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
													<link itemprop="availability" href="http://schema.org/LimitedAvailability" />{l s='Product available with different options' mod='wtproductfilter'}

											{else}
													<link itemprop="availability" href="http://schema.org/OutOfStock" />{l s='Out of stock' mod='wtproductfilter'}
											{/if}
										{/if}
										{hook h="displayProductPriceBlock" product=$product type="price"}
										{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{/if}
								</div>
							{/if}
						</div>
						
					</div>
				{if $smarty.foreach.product_list.iteration % $number_line == 0 ||$smarty.foreach.product_list.last|| $number_line == 1}
				</div>
				{/if}
			{/foreach}
		</div>
	</div>
{/foreach}		
<script type="text/javascript">
$(document).ready(function() {
	$(".owlslider-prod-filter").owlCarousel({
	  loop: false,
		responsive: {
			0: { items: 1},
			464:{ items: 2},
			750:{ items: 3},
			974:{ items: 4},
			1170:{ items: 5}
		},
	  dots: false,
	  nav: true
	  });
});
</script>