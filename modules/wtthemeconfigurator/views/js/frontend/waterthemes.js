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

function isMobileIpad() {
	if(navigator.userAgent.match(/Android/i) ||
		navigator.userAgent.match(/webOS/i) ||
		navigator.userAgent.match(/iPad/i) ||
		navigator.userAgent.match(/iPhone/i) ||
		navigator.userAgent.match(/iPod/i)
		){
			return true;
	}
	else return false;
}

function isMobile() {
	if(navigator.userAgent.match(/Android/i) ||
		navigator.userAgent.match(/webOS/i) ||
		navigator.userAgent.match(/iPhone/i) ||
		navigator.userAgent.match(/iPod/i)
		){
			return true;
	}
	else return false;
}

$(function(){
	var scrol_to_top = 1;
	if (typeof scrol_to_top !== "undefined" && scrol_to_top && !isMobileIpad())
	{
		if (typeof scrol_to_top_text === "undefined" || !scrol_to_top_text)
			scrol_to_top_text = 'to top';
		var htmlToTop = "<div id='wt_scroll_top' data-toggle='tooltip' data-placement='top' title='" + scrol_to_top_text + "'>" + scrol_to_top_text + "</div>";
		$('body').append(htmlToTop);
		$(window).scroll(function(){
			if($(this).scrollTop()!=0)
				$('#wt_scroll_top').fadeIn();
			else
				$('#wt_scroll_top').fadeOut();
		});
		$('#wt_scroll_top').click(function(){
			$('body,html').animate({scrollTop:0},500);
		});
	}
});

$(function ()
{
   $.srSmoothscroll({
		step: 120,
		speed: 300
   });
})

$(document).ready(function()
{
	$("html").niceScroll();
	searchdropDown();
});
function searchdropDown()
{
	elementClick1 = '#search_block_top .search_hover';
	elementSlide1 = '#search_block_top .drop_search';
	activeClass1 = 'active';

	$(elementClick1).on('click', function(e){
		e.stopPropagation();
		var subUl = $(this).next(elementSlide1);
		if(subUl.is(':hidden'))
		{
			subUl.slideDown();
			$(this).addClass(activeClass1);
		}
		else
		{
			if($(window).width()>=768)
			{
				subUl.slideUp();
				$(this).removeClass(activeClass1);
			}
		}
		if($(window).width()>=768)
		{
			$(elementClick1).not(this).next(elementSlide1).slideUp();
			$(elementClick1).not(this).removeClass(activeClass1);
		}
		e.preventDefault();
	});
	$(elementSlide1).on('click', function(e){
		e.stopPropagation();
	});
	$(document).on('click', function(e){
		if($(window).width()>=768)
		{
			e.stopPropagation();
			var elementHide1 = $(elementClick1).next(elementSlide1);
			$(elementHide1).slideUp();
			$(elementClick1).removeClass('active');
		}
	});
	$(window).resize(function(){
		if($(window).width()<768)
			$('#search_block_top .drop_search').show();
		else
			$('#search_block_top .drop_search').hide();
	});	
}