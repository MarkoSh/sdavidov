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

<!-- Module Product From Category -->
<div class="wt-prod-filter-topcolumn wt-col-sm-12 wt-col-md-2">
	<div class="block-content">
		{foreach from=$group_prod_fliter item=product_hook name=product_hook}
			{if $product_hook.type_display == 'column'}
				{$product_groups = $product_hook.product_group}
				{$number_line = 4}
				{$id_lang = Context::getContext()->language->id}
				{foreach from = $product_groups item = product_group name = product_group}
					<div class="{$product_group.class}">
						<div class="owlslider-prod-filter product_list">
						{if isset($product_group.product_list) && count($product_group.product_list) > 0}
							{foreach from=$product_group.product_list item=product name=product_list}
								{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
								<div class="item ajax_block_product">
								{/if}
									<div class="product-container">
										<div class="left-block">
											<div class="product-image-container">
												<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$product.legend|escape:html:'UTF-8'}" /></a>
												{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
													<div class="wt-label">
														<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
															{if $product.specific_prices.reduction_type == 'percentage'}
																<span class="sale-label">
																	-{$product.specific_prices.reduction*100}%
																</span>
															{else}
																<span class="sale-label">
																	-{convertPrice price=$product.reduction}
																</span>
															{/if}
														</a>
													</div>
												{/if}
											</div>
										</div>
										<div class="right-block">
											{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
												<div class="content_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
													{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
														<span itemprop="price" class="price product-price">
															{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
														</span>
														<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
														{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
															{hook h="displayProductPriceBlock" product=$product type="old_price"}
															<span class="old-price product-price">
																{displayWtPrice p=$product.price_without_reduction}
															</span>
															{if $product.specific_prices.reduction_type == 'percentage'}
																<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
															{/if}
														{/if}
														{hook h="displayProductPriceBlock" product=$product type="price"}
														{hook h="displayProductPriceBlock" product=$product type="unit_price"}
													{/if}
												</div>
											{/if}
											<h5 class="product-name"><a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.legend|escape:html:'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></h5>	
										</div>
										{hook h='displayCountDownProduct' product=$product prev_id='pftc'}										
									</div>
								{if $smarty.foreach.product_list.iteration % $number_line == 0 ||$smarty.foreach.product_list.last|| $number_line == 1}
								</div>
								{/if}
							{/foreach}
						{else}
							<div class="item product-box ajax_block_product">
								<p class="alert alert-warning">{l s='No product at this time' mod='wtproductfilter'}</p>
							</div>
						{/if}
						</div>
					</div>
				{/foreach}
				{if $product_hook.use_slider == 1}
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
				{/if}
			{/if}
		{/foreach}
	</div>
</div>
<!-- /Module Product From Category -->