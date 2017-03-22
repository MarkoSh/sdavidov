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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(window).load(function()
{
	$(document).on('click', '#wt-menu-ver-left .icon-drop-mobile', function() {
		 $(this).next().toggle('slow');
		$(this).toggleClass('opened');
	});
	if($(window).width() > 750)
	{
		//$("#index .wt-menu-ver-left .category-left").css('display','block');
		menuVerHover();
	}	
	else
	{
		$("#index .wt-menu-ver-left .category-left").css('display','none');
		menuVerClick();
	}
		
	var width_menu_content = $('#columns').width() - $('#wt-menu-ver-left').width();
	$('#wt-menu-ver-left ul.menu-content li div.wt-sub-menu').each(function(index, element)
	{
		var width_sub = parseInt($(this).children('.v-menu-sub-width').val());
		if($(window).width() < 1024 && width_sub >= 6)
			width_sub = 12;
		if($(window).width() < 1024 && width_sub < 6)
			width_sub = 6;
		
		var width_sub_result = parseInt(width_menu_content/12*width_sub);
		$(this).width(width_sub_result);
	});
});

$(window).resize(function()
{
	var width_menu_content = $('#columns').width() - $('#wt-menu-ver-left').width();
	$('#wt-menu-ver-left ul.menu-content li div.wt-sub-menu').each(function(index, element)
	{
		var width_sub = parseInt($(this).children('.v-menu-sub-width').val());
		if($(window).width() < 1024 && width_sub >= 6)
			width_sub = 12;
		if($(window).width() < 1024 && width_sub < 6)
			width_sub = 6;
		
		var width_sub_result = parseInt(width_menu_content/12*width_sub);
		$(this).width(width_sub_result);
	});
	
	if($(window).width() < 750)
	{
		$("#index .wt-menu-ver-left .category-left").css('display','none')
		$(".wt-menu-ver-left .category-title").unbind()
		menuVerClick();
	}
	else
	{
		//$("#index .wt-menu-ver-left .category-left").css('display','block')
		menuVerHover();
	}
});

function menuVerHover()
{
	var ul_ver_menu = new HoverWatcher('.wt-menu-ver-page .category-left');
	var ver_menu_title = new HoverWatcher('.wt-menu-ver-page .category-title');
	
	$(".wt-menu-ver-page .category-title").hover(
		function() {
			$(".wt-menu-ver-page .category-left").stop(true, true).slideDown(400);
		},
		function() {
			setTimeout(function() {
				if (!ul_ver_menu.isHoveringOver() && !ver_menu_title.isHoveringOver()){
					$(".wt-menu-ver-page .category-left").stop(true, true).slideUp(200);
				}
			}, 200);
		}
	);
	
	$(".wt-menu-ver-page .category-left").hover(
		function() {
			$(".wt-menu-ver-page .category-left").stop(true, true).slideDown(400);				
		},
		function() {
			setTimeout(function() {
				if (!ul_ver_menu.isHoveringOver())
					$(".wt-menu-ver-page .category-left").stop(true, true).slideUp(200);
			}, 200);
		}
	);	
}
function menuVerClick()
{
	 $(".wt-menu-ver-left .category-title").toggle(
			function() {
				$(".wt-menu-ver-left .category-left").stop(true, true).slideDown(400);
			},
			function() {			
				$(".wt-menu-ver-left .category-left").stop(true, true).slideUp(200);		
			}
		);
}