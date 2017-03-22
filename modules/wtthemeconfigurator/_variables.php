<?php
/**
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
*/

	$stores = array(
			'default'
	);
	$themes_colors = array(
			'default',
	);
	
	$items_settings = array(
		'body_image' => array(
			'text' => 'Background image of body',
			'note' => 'Support Box Layout Only',
			'attr_css' => 'background-image',
			'selector' => 'body',
			'frontend' => true,
		),
		'body_color' => array(
			'text' => 'Background color of body',
			'note' => 'Support Box Layout Only',
			'attr_css' => 'color',
			'selector' => array(
				'background-color' => 'body',
			),
			'frontend' => true,
		),
		'content_bkg' => array(
			'text' => 'Background content',
			'attr_css' => 'color',
			'selector' => array(
				'background-color' =>'#index .columns-container,.columns-container',
			),
			'frontend' => false,
		),
		'header_footer_bkg' => array(
			'text' => 'Background header and footer',
			'attr_css' => 'color',
			'selector' => array(
				'background-color' =>'.header-container,.slideshow-container,.footer-container,#page #footer_bottom .container,#page .header-container .container',
			),
			'frontend' => false,
		),
		'main_color' => array(
			'text' => 'Main color',
			'note' => 'background title sidebar',
			'attr_css' => 'color',
			'selector' => array(
				'background-color' =>'',
				'color' =>'',
				'border-color' =>'',
				'background-color_10'=>'',
				'border-color_10' =>'',
				'background-color_-20' => ''
				),
			'frontend' => false,
		),
		'body_font' => array(
			'text' => 'Font of body',
			'note' => 'text desction, link footer,...',
			'attr_css' => 'font-family',
			'selector' => 'body,h1, h2, h3, h4, h5, h6',
			'frontend' => true,
		),
		'second_font' => array(
			'text' => 'Second font',
			'note' => 'Menu,title product,price,...',
			'attr_css' => 'font-family',
			'selector' => '#page .page-heading,#page .page-subheading,.name_product a, .block .products-block li .product-content h5 a, .product_list h5 a, .subcategory-name, .product_list h5 a,#menu ul li a.title_menu_parent,.ui-tabs-nav li a,.price,old-price,new-label,.price-percent-reduction span,#home-page-tabs > li a,.block .title_block,.block h4,h3.page-product-heading span,ul.step li a, ul.step li span,.breadcrumb,.footer-container #footer h4,.heading-counter,#cart_summary tfoot td,.cart_voucher h4,#currencies-block-top .current, #languages-block-top .current,#categories_block_top > a,.quick-view,#add_to_cart .exclusive, .button.ajax_add_to_cart_button,#header_links li,#menu > ul > li > a.title_menu_parent span,.header_user_info,#categories_block_left li a,#shopping_cart > a,#columns #layered_block_left .layered_subtitle,.alert,h3.box-heading > span, #subcategories > span, .homecategoryfeature h4 > span, #subcategories p.subcategory-heading, .homecategoryfeature h4 a,#page #shopping_cart .title_block_cart,#page #shopping_cart > a,#page .cart_block .cart-prices,#menu .menu_h3 a,#order-opc .page-heading.step-num,#account-creation_form h3.page-subheading,#my-account ul.myaccount-link-list li a,.blog_block h3.title span,.btn-default,.button.exclusive-medium,.pb-center-column h5 span,#wishlist_button,#show_result h3 a,#show_result h1',
			'frontend' => true,
		)
	);
