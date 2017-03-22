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
*  @author    Codespot SA <support@presthemes.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function()
{
	$('.show-banner-special').hide();
	$(document).on('click', '.close-banner-special', function() {
		$('.banner-special-content').hide('slow');
		$('#wt_overlay').css('display','none');
		var date = new Date();
		date.setTime(date.getTime() + (cookies_time * 1000));
		$.cookie('wt_hide_banner_special', cookies_time, {expires: date});
		$(this).hide();
		$('.show-banner-special').show();
	});
	
	$(document).on('click', '.show-banner-special', function() {
		$('#wt_overlay').css('display','block');
		$('.banner-special-content').show('slow');
		$.cookie('wt_hide_banner_special', null);
		$(this).hide();
		$('.close-banner-special').show();
	});
	
	if($.cookie('wt_hide_banner_special') && $.cookie('wt_hide_banner_special') > 0)
	{
		$('.banner-special-content').css('display','none');
		$('#wt_overlay').css('display','none');
		$('.show-banner-special').show();
		$('.close-banner-special').hide();
	}
	else
	{
		$('.banner-special-content').show('slow');
		$('#wt_overlay').css('display','block');
		$('.show-banner-special').hide();
		$('.close-banner-special').show();
	}
	
	$(document).on('click', '#wt_overlay', function() {
		$('.banner-special-content').hide('slow');
		$('#wt_overlay').css('display','none');
		$('.close-banner-special').hide();
		 var date = new Date();
		date.setTime(date.getTime() + (cookies_time * 1000));
		$.cookie('wt_hide_banner_special', cookies_time, {expires: date});
		$(this).hide();
		$('.show-banner-special').show();
	});
});