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
*  @author    Codespot SA <support@presthemes.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="demo_frontend" class="demo_frontend_close">
	<div id="demo_icon_set">
		<i class="icon-cogs icon-2x"></i>
	</div>
	<div id="demo_container">
		<ul>
			<li>
				<span class="head">{l s='Mode Css' mod='wtthemeconfigurator'}</span>
				<div class="demo_content">
					<div class="radio-inline">
						<label>
							<input type="radio" name="mode_css" value="box" {if !isset($smarty.cookies.mode_css_input) && isset($options_admin.box_mode) && $options_admin.box_mode == 1}checked="checked"{elseif isset($smarty.cookies.mode_css_input) && $smarty.cookies.mode_css_input == 'box'}checked="checked"{/if}> {l s='Box' mod='wtthemeconfigurator'}
						</label>
					</div>
					<div class="radio-inline">
						<label>
							<input type="radio" name="mode_css" value="wide" {if !isset($smarty.cookies.mode_css_input) && isset($options_admin.box_mode) && $options_admin.box_mode == 0}checked="checked"{elseif isset($smarty.cookies.mode_css_input) && $smarty.cookies.mode_css_input == 'wide'}checked="checked"{/if}>{l s='Wide' mod='wtthemeconfigurator'}
						</label>
					</div>
				</div>
			</li>
			{if count($templates)>1}
			<li>
				<span class="head">{l s='Color template' mod='wtthemeconfigurator'}</span>
				<div class="demo_content clearfix" style="display:none;">
					{foreach from=$templates item=template}
						<div class="bg_template {if !isset($smarty.cookies.color_template) && isset($options_admin.template) && $options_admin.template == $template}active{elseif isset($smarty.cookies.color_template) && $smarty.cookies.color_template == $template}active{/if}" title="{$template|escape:'html':'UTF-8'}"><label class="{$template|escape:'html':'UTF-8'}">
						<input type="radio" name="color_template" value="{$template|escape:'html':'UTF-8'}"/>{$template|escape:'html':'UTF-8'}</label></div>
					{/foreach}
				</div>
			</li>
			{/if}
			<li>
				<span class="head">{l s='Background body' mod='wtthemeconfigurator'}<em style="font-size:10px;"> {l s='(Box Layout Only)' mod='wtthemeconfigurator'}</em></span>
				<div class="demo_content" style="display:none;">
					<div class="pattern">
					<span class="pattern_item" id="pattern_no_img"style="background-image:url({$path_img|escape:'html':'UTF-8'}patterns/no_img.jpg); width:30px; height:30px; display:inline-block"></span>
					{foreach from=$patterns item=pattern}
					{assign var=id_pattern value="."|explode:$pattern}
						<span class="pattern_item" id="pattern_{$id_pattern[0]|escape:'html':'UTF-8'}"style="background-image:url({$path_img|escape:'html':'UTF-8'}patterns/{$pattern|escape:'html':'UTF-8'}); width:30px; height:30px; display:inline-block"></span>
					{/foreach}
					</div>
					{if isset($body_col)}
					{assign var=color_body_color value=$body_col[1]}
					<div class="color_item clearfix">
						<span>{$body_col[0]|escape:'html':'UTF-8'}</span>
						<div id="{$body_col[1]|escape:'html':'UTF-8'}" class="bg_color_setting {if !isset($smarty.cookies.color_body_color) && isset($options_admin.color_body_color)}{$options_admin.color_body_color|escape:'html':'UTF-8'}{/if}" style="cursor:pointer;{if !isset($smarty.cookies.color_body_color) && isset($options_admin.color_body_color) && $options_admin.color_body_color != ''}background-color:{$options_admin.color_body_color|escape:'html':'UTF-8'}{elseif isset($smarty.cookies.color_body_color) && $smarty.cookies.color_body_color != ''}background-color:{$smarty.cookies.color_body_color|escape:'html':'UTF-8'}{/if}">text</div>
					</div>
					{/if}
				</div>
			</li>
			{if count($color_bgs)>0}
			<li>
			<span class="head">{l s='Color' mod='wtthemeconfigurator'}</span>
			<div class="demo_content" style="display:none;">
			{foreach from=$color_bgs item=color_bg key=elem_color}
				<div class="color_item clearfix">
					<span>{$color_bg[0]|escape:'html':'UTF-8'}</span>
					<div id="{$elem_color|escape:'html':'UTF-8'}" class="bg_color_setting " style="cursor:pointer;{if !isset($smarty.cookies.$elem_color) && isset($options_admin.$elem_color) && $options_admin.$elem_color != ''}background-color:{$options_admin.$elem_color|escape:'html':'UTF-8'}{elseif isset($smarty.cookies.$elem_color) && $smarty.cookies.$elem_color != ''}background-color:{$smarty.cookies.$elem_color|escape:'html':'UTF-8'}{/if}">text</div>
					
					{if isset($color_bg[0])}<span class="note">{$color_bg[1]|escape:'html':'UTF-8'}</span>{/if}
				</div>
			{/foreach}
			</div>
			</li>
			{/if}
			{if count($font_list_demo)>0}
			<li>
				<span class="head">{l s='Font' mod='wtthemeconfigurator'}</span>
				<div class="demo_content" style="display:none;">
				{foreach from=$font_list item=font key=elem_font}
					<div class="font_item">
						<p><label>{$font[0]|escape:'html':'UTF-8'}</label></p>
						<select name="{$elem_font|escape:'html':'UTF-8'}" id="{$elem_font|escape:'html':'UTF-8'}" onchange="showResultChooseFont('{$elem_font|escape:'html':'UTF-8'}','{$font[1]|escape:'html':'UTF-8'}')" class="form-control">
						{foreach from=$font_list_demo item=font_demo key=elem_font_demo}
							{$font_demo|escape:'quotes':'UTF-8'}
						{/foreach}
						</select>
					</div>
					<script type="text/javascript">	
						$(document).ready(function() {
							$("#{$elem_font|escape:'html':'UTF-8'}").val('{if !isset($smarty.cookies.$elem_font) && isset($options_admin.$elem_font) && $options_admin.$elem_font != ''}{$options_admin.$elem_font|escape:"html":"UTF-8"}{elseif isset($smarty.cookies.$elem_font) && $smarty.cookies.$elem_font != ''}{$smarty.cookies.$elem_font|escape:"html":"UTF-8"}{/if}');
						});
					</script>
				{/foreach}
				</div>
			</li>
			{/if}
		</ul>
		<div class="reset"><input type="button" class="btn btn-default button" id="cs_reset_setting" value="{l s='Reset' mod='wtthemeconfigurator'}" /></div>
	</div>
</div>
<script type="application/javascript">
function ColorEv (i,selector)
{
	$("#" + i).ColorPicker({
		color: "#0000ff",
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$("#" + i).css("background", "#" + hex);
			$.cookie("" + i + "", "#" + hex);
			for(var key in selector)
			{
				if(key)
				{
					if(key.indexOf("_")!=-1)
					{
						var arrKey=key.split('_');
						var new_key=arrKey[0];
						var percent=parseInt(arrKey[1])*-1;
						var NewColor = LightenDarkenColor($.cookie(i),percent);
						$("" + selector[key] + "").css("" + new_key + "","" +NewColor+ "");
					}
					else
						$("" + selector[key] + "").css("" + key + "","" + $.cookie(i) + "");
				}
			}		
			
		}
	});
	if ($.cookie("" + i + "") != null)
	{
		$("#" + i + "").css("background","" + $.cookie(i) + "");
		for(var key in selector)
			if(key)
				{
				if(key.indexOf("_")!=-1)
					{
						var arrKey=key.split('_');
						var new_key=arrKey[0];
						var percent=parseInt(arrKey[1])*-1;
						var NewColor = LightenDarkenColor($.cookie(i),percent);
						$("" + selector[key] + "").css("" + new_key + "","" +NewColor+ "");
					}
					else
						$("" + selector[key] + "").css("" + key + "","" + $.cookie(i) + "");
				}
	}
}
$(document).ready(function() {
	$('#demo_icon_set').toggle(
		function(){
			$('#demo_frontend').animate({
				left:'5px',
			}, 500);					
		},
		function(){
			$('#demo_frontend').animate({
				left:'-276px',
			}, 500);     
	});
	var data={$config_data|escape:'quotes':'UTF-8'};
	
	for(var key in data)
	{
		if(key)
			ColorEv(key,data[key]["selector"]);
	}
	var data_body_col = {$config_body_col|escape:'quotes':'UTF-8'};
	ColorEv(data_body_col[1],data_body_col["selector"]["color"]);
	
	/*background-pattern*/
	{foreach from=$patterns item=pattern name=patterns}
		{assign var=id_pattern value="."|explode:$pattern}
		$("#pattern_{$id_pattern[0]|escape:'html':'UTF-8'}").click(function() {
			$(".pattern_item").removeClass("active");
			$(this).addClass("active");			
			var url_pattern = "{$path_img|escape:'html':'UTF-8'}patterns/{$pattern|escape:'html':'UTF-8'}";
			$.cookie('cookie_bg_pattern',url_pattern);
			$.cookie('cookie_bg_pattern_class','pattern_{$id_pattern[0]|escape:"html":"UTF-8"}');
			$('body').css('background-image', 'url("' + $.cookie('cookie_bg_pattern') + '")');
			$('body').css('background-repeat', 'repeat');
		});
		
		$("#pattern_no_img").click(function(){
			$.cookie('cookie_bg_pattern',null);
			$("#" + $.cookie('cookie_bg_pattern_class') + "").removeClass("active");
			$.cookie('cookie_bg_pattern_class',null);
			$('body').css('background-image', 'none');
			$(this).addClass("active");
		});
		
		if($.cookie('cookie_bg_pattern_class'))
		{
			$("#" + $.cookie('cookie_bg_pattern_class') + "").addClass("active");
		}
	{/foreach}	
});
function LightenDarkenColor(col, amt) {
    var usePound = false;
    if (col[0] == "#") {
        col = col.slice(1);
        usePound = true;
    }
    var num = parseInt(col,16);
    var r = (num >> 16) + amt;
    if (r > 255) r = 255;
    else if  (r < 0) r = 0;
    var b = ((num >> 8) & 0x00FF) + amt;
 
    if (b > 255) b = 255;
    else if  (b < 0) b = 0;
 
    var g = (num & 0x0000FF) + amt;
 
    if (g > 255) g = 255;
    else if (g < 0) g = 0;
 
    return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16);
  
}
</script>