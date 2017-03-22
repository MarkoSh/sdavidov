<?php
/**
* 2007-2014 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2014 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SampleDataCustomHtml
{
	public function initData()
	{
		$result = true;
		$id_shop = Configuration::get('PS_SHOP_DEFAULT');
		
		$block1 = '<div class="custom-bottom-banner col-xs-12"><a title="bottom banner" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/bn-center.jpg" alt="" /></a>\r\n<div class="content-bn">\r\n<h3>run</h3>\r\n<h3>donot hide</h3>\r\n<p>Lorem ipsum dolor sit amet aliquam</p>\r\n<img src="{static_block_url}themes/wt_cr7/img/cms/st-logo.png" alt="" /></div>\r\n</div>\r\n<p></p>';
		
		$block2 = '<div class="custom-payment row">\r\n<div class="col-xs-12">\r\n<h4>We accept the following forms of payment</h4>\r\n<ul>\r\n<li><a title="visa" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/visa.png" alt="" /></a></li>\r\n<li><a title="master card" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/master_cart.png" alt="" /></a></li>\r\n<li><a title="american" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/american.png" alt="" /></a></li>\r\n<li><a title="paypal" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/paypal.png" alt="" /></a></li>\r\n<li><a title="rss" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/rss.png" alt="" /></a></li>\r\n</ul>\r\n</div>\r\n</div>';
		
		$block3 = '<div class="row">\r\n<div class="content-left col-xs-12 col-sm-8">\r\n<div class="link_footer"><a title="about us" href="#">About us</a> <a title="customer service" href="#">Customer service</a> <a title="site map" href="#">Site map</a> <a title="search terms" href="#">Search terms</a> <a title="order and return" href="#">Order and return</a> <a title="contact us" href="#">Contact us</a></div>\r\n</div>\r\n<div class="content-left col-xs-12 col-sm-4">\r\n<p>© 2015 CR7 Sport Demo Store. All Rights Reserved.</p>\r\n</div>\r\n</div>';
		
		$block4 = '<div class="bn-top-home clearfix">\r\n<ul>\r\n<li class="col-xs-12 col-sm-4"><a href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/bn_st_1.jpg" alt="" /></a></li>\r\n<li class="col-xs-12 col-sm-4"><a href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/bn_st_3.jpg" alt="" /></a></li>\r\n<li class="col-xs-12 col-sm-4"><a href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/bn_st_2.jpg" alt="" /></a></li>\r\n</ul>\r\n</div>';
		
		$block5 = '<ul class="wt-custom-service clearfix">\r\n<li class="col-xs-12 col-sm-4">\r\n<div class="type-text">\r\n<h3><a title="outdoor" href="#">outdoor</a></h3>\r\n<p>Lorem ipsum dolor sit consectetur adid elit</p>\r\n</div>\r\n</li>\r\n<li class="col-xs-12 col-sm-4">\r\n<div class="type-text">\r\n<h3><a title="running" href="#">running</a></h3>\r\n<p>Praesent hendrerit rhoncus mauris</p>\r\n</div>\r\n</li>\r\n<li class="col-xs-12 col-sm-4 last">\r\n<div class="type-text">\r\n<h3><a title="accessories" href="#">accessories</a></h3>\r\n<p>Vestibulum ante ipsum primis</p>\r\n</div>\r\n</li>\r\n</ul>';
		
		$block6 = '<div class="wt-banner-home">\r\n<div class="block-title">\r\n<h3>Explore More</h3>\r\n</div>\r\n<ul class="row">\r\n<li class="col-xs-12 col-sm-4"><a class="banner-home-img" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/st-1.jpg" alt="" /></a>\r\n<h3>MENS SHOES</h3>\r\n<p>Donec dapibus ipsum sit amet metus</p>\r\n</li>\r\n<li class="col-xs-12 col-sm-4"><a class="banner-home-img" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/st-2.jpg" alt="" /></a>\r\n<h3>new arrivals</h3>\r\n<p>Proin efficitur diam sit amet auctor</p>\r\n</li>\r\n<li class="col-xs-12 col-sm-4"><a class="banner-home-img" href="#"><img src="{static_block_url}themes/wt_cr7/img/cms/st-3.jpg" alt="" /></a>\r\n<h3>featured</h3>\r\n<p>Pellentesque dui lacus sit amet</p>\r\n</li>\r\n</ul>\r\n</div>';
		
		$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'wtcustomhtml` (`id_wtcustomhtml`, `hook`, `active`) 
			VALUES
			(1, "displayBottomHome", 1),
			(2, "displayBottomFooter", 1),
			(3, "displayCopyRight", 1),
			(4, "displayBottomTop", 1),
			(5, "displayTopHome", 1),
			(6, "displayHome", 1);'); 
		
		$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'wtcustomhtml_shop` (`id_wtcustomhtml`, `id_shop`,`active`) 
			VALUES 
			(1,'.$id_shop.', 1),
			(2,'.$id_shop.', 1),
			(3,'.$id_shop.', 1),
			(4,'.$id_shop.', 1),
			(5,'.$id_shop.', 1),
			(6,'.$id_shop.', 1)
			;');
			
		foreach (Language::getLanguages(false) as $lang)
		{
			$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'wtcustomhtml_lang` (`id_wtcustomhtml`, `id_shop`, `id_lang`, `title`, `content`) 
			VALUES 
			( "1", "'.$id_shop.'","'.$lang['id_lang'].'","Home bottom banner", \''.$block1.'\'),
			( "2", "'.$id_shop.'","'.$lang['id_lang'].'","Payment", \''.$block2.'\'),
			( "3", "'.$id_shop.'","'.$lang['id_lang'].'","Copy Right", \''.$block3.'\'),
			( "4", "'.$id_shop.'","'.$lang['id_lang'].'","Banner BottomTop", \''.$block4.'\'),
			( "5", "'.$id_shop.'","'.$lang['id_lang'].'","Support service", \''.$block5.'\'),
			( "6", "'.$id_shop.'","'.$lang['id_lang'].'","Banner home", \''.$block6.'\');');
		}
		return $result;
	}
}