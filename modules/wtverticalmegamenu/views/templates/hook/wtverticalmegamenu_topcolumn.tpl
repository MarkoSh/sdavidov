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

<!-- Module Megamenu-->
<div id="wt-menu-ver-left" class="wt-menu-ver-left wt-col-sm-3 wt-col-md-2">
{$id_lang = Context::getContext()->language->id}
	<div class="category-left">
	<div class="title-menu-mobile category-title"><span>{l s='Category' mod='wtverticalmegamenu'}</span></div>
	<ul class="menu-content">
		{foreach from=$menus item=menu name=menus}
			{if isset($menu.type) && $menu.type == 'CAT' && $menu.dropdown == 1}
				{$menu.sub_menu|escape:'quotes':'UTF-8'}
			{else}
				<li class="level-1 {$menu.class|escape:'html':'UTF-8'}{if count($menu.sub_menu) > 0}parent{/if}">
					
					<a href="{$menu.link|escape:'html':'UTF-8'}">
					    {if $menu.type_icon == 0 && $menu.icon != ''}
						<img class="img-icon" src="{$icon_path|escape:'html':'UTF-8'}{$menu.icon|escape:'html':'UTF-8'}" alt=""/>
					    {elseif  $menu.type_icon == 1 && $menu.icon != ''}
						<i class="{$menu.icon|escape:'html':'UTF-8'}"></i>
					    {/if}
					   <span>{$menu.title|escape:'html':'UTF-8'}</span>
					  
					</a>
					{if $menu.subtitle != ''}<span class="menu-subtitle">{$menu.subtitle|escape:'html':'UTF-8'}</span>{/if}
					{if isset($menu.sub_menu) && count($menu.sub_menu) > 0}
						<span class="icon-drop-mobile"></span>
						<div class="wt-sub-menu menu-dropdown {$menu.align_sub|escape:'html':'UTF-8'}">
							<input type="hidden" value="{$menu.width_sub|intval}" name="v-menu-sub-width" class="v-menu-sub-width"/>
							<div class="col-xs-12">
							{foreach from=$menu.sub_menu item= menu_row name=menu_row}
								<div class="wt-menu-row row {$menu_row.class|escape:'html':'UTF-8'}">
									{if isset($menu_row.list_col) && count($menu_row.list_col) > 0}
										{foreach from=$menu_row.list_col item= menu_col name=menu_col}
											<div class="wt-menu-col col-xs-12 {$menu_col.width|escape:'html':'UTF-8'} {$menu_col.class|escape:'html':'UTF-8'}">
												{if count($menu_col.list_menu_item) > 0}
													<ul class="ul-column">
													{foreach from=$menu_col.list_menu_item item= sub_menu_item name=sub_menu_item}
														<li class="menu-item {if $sub_menu_item.type_item == 1} item-header{else} item-line{/if}">
															{if $sub_menu_item.type_link == 4}
																{$id_lang = Context::getContext()->language->id}
																{$id_lang = Context::getContext()->language->id}
																{foreach from = $sub_menu_item.product item=product name=product}
																<div class="product-container clearfix">
																	<h5 class="product-name">
																		<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
																			{$product.name|truncate:25:''|escape:'html':'UTF-8'}
																		</a>
																		
																	</h5>
																	{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
																		<div class="content_price">
																			{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
																				<span class="price product-price">
																					{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
																				</span>
																				{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
																					{hook h="displayProductPriceBlock" product=$product type="old_price"}
																					<span class="old-price product-price">
																						{displayWtPrice p=$product.price_without_reduction}
																					</span>
																					{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
																					{if $product.specific_prices.reduction_type == 'percentage'}
																						<span class="price-percent-reduction">-{$product.specific_prices.reduction|escape:'html':'UTF-8' * 100}%</span>
																					{/if}
																				{/if}
																				{hook h="displayProductPriceBlock" product=$product type="price"}
																				{hook h="displayProductPriceBlock" product=$product type="unit_price"}
																			{/if}
																		</div>
																	{/if}
																	
																	<div class="product-image-container">
																		<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
																			<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}"  itemprop="image" />
																		</a>
																		{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
																			<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
																				<span class="sale-label">{l s='Sale' mod='wtverticalmegamenu'}</span>
																			</a>
																		{/if}
																	</div>
																</div>
																{/foreach}
															{else if $sub_menu_item.type_link == 3}
																<a class="title-html-block" href="{$sub_menu_item.link|escape:'html':'UTF-8'}">{$sub_menu_item.title|escape:'html':'UTF-8'}</a>
																<div class="html-block">
																	{$sub_menu_item.text|escape:'quotes':'UTF-8'}
																</div>
															{else}
																<a href="{$sub_menu_item.link|escape:'html':'UTF-8'}">{$sub_menu_item.title|escape:'html':'UTF-8'}</a>
															{/if}
														</li>
													{/foreach}
													</ul>
												{/if}
											</div>
										{/foreach}
									{/if}
								</div>
							{/foreach}
							</div>
						</div>
					{/if}
				</li>
			{/if}
		{/foreach}
	</ul>
	</div>
</div>
<!-- /Module Megamenu -->