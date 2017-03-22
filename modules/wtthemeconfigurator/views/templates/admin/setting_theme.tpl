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

{if count($stores)>1}
<form class="form-horizontal" action="{$postAction|escape:'html':'UTF-8'}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="submitlayout" value="1">
<div class="panel" style="display:none">
	<h3>
		<i class="icon-cogs"></i>{l s='Settings Layout' mod='wtthemeconfigurator'}
	</h3>
	<div class="form-group">
		<label class="control-label col-lg-2">{l s='Choose a Layout' mod='wtthemeconfigurator'}</label>
		<div class="col-lg-10 {$template_current|escape:'html':'UTF-8'}">
			<div class="row">
			{foreach from=$stores item = store key = k}
				<div class="bg_store{if $stores_select==$store} active{/if}" title="{$store|escape:'html':'UTF-8'}">
				<label class="{$store|escape:'html':'UTF-8'}">
				<div class="radio"><span>
					<input type="radio" {if $stores_select==$store}checked="checked" {/if} name="store" value="{$store|escape:'html':'UTF-8'}"/></span>
				</div>
				</label>
				<img src="{$path_img|escape:'html':'UTF-8'}layout/{$store|escape:'html':'UTF-8'}.jpg" title="{$store|escape:'html':'UTF-8'}"/>
				</div>
			{/foreach}
			</div>
			<p class="help-block" style="font-size:11px">{l s='When the layout changes, there will be an impact on modules: status: enable / disable, config and position.' mod='wtthemeconfigurator'}</p>
		</div>
		
	</div>
	<div class="panel-footer">
		<button type="button" value="1" id="submit_layout" name="submitlayout" class="btn btn-default pull-right" onclick="noteCustomer(this);">
			<i class="process-icon-save"></i> {l s='Save' mod='wtthemeconfigurator'}
		</button>
	</div>
</div>
</form>
{/if}
<form class="form-horizontal" action="{$postAction|escape:'html':'UTF-8'}" method="POST" enctype="multipart/form-data">
<div class="panel" style="display:none">
	<h3>
		<i class="icon-cogs"></i> {l s='Settings style' mod='wtthemeconfigurator'}
	</h3>
	{if count($templates) > 1}
	<div class="form-group">
		<label class="control-label col-lg-2">{l s='Choose a color template' mod='wtthemeconfigurator'}</label>
		<div class="col-lg-10 {$template_current|escape:'html':'UTF-8'}">
			
			{foreach from=$templates item = template key = k}
				<div class="bg_template{if isset($template_current) && $template_current == $template} active{/if}" title="{$template|escape:'html':'UTF-8'}"><label class="{$template|escape:'html':'UTF-8'}">
				<div class="radio"><span><input type="radio" {if $template_current == $template}checked="checked"{/if} name="template" value="{$template|escape:'html':'UTF-8'}"/></span></div>{$template|escape:'html':'UTF-8'}</label></div>
			{/foreach}
		</div>
	</div>
	{/if}
	<div class="form-group">
		<label for="box_mode" class="control-label col-lg-3 ">
			{l s='Box mode' mod='wtthemeconfigurator'}
		</label>
		<div class="col-lg-9">
			<div class="row">
				<div class="input-group col-lg-2">
					<span class="switch prestashop-switch">
						<input type="radio" name="box_mode" id="box_mode_on" value="1" {if isset($options.box_mode) && $options.box_mode == 1} checked="checked" {/if}>
						<label for="box_mode_on">Yes</label>
						<input type="radio" name="box_mode" id="box_mode_off" value="0" {if isset($options.box_mode) && $options.box_mode == 0} checked="checked" {/if}>
						<label for="box_mode_off">No</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
	</div>
	{foreach from=$bg_images item=list key=name}
		{$list_style = $list.attr_css}
			{assign var=type_image  value=type_image_|cat:$name}
			{assign var=bg_img  value=background_image_|cat:$name}
			{assign var=repeat  value=background_repeat_|cat:$name}			
			<div class="form-group">
			<label class="control-label col-lg-3">{$list.text|escape:'html':'UTF-8'}</label>
			<div class="col-lg-9">
			<div class="col-lg-9">						
				<div class="form-group"><input type="radio" {if isset($options.$type_image) && $options.$type_image == 'pattern'}checked="checked"{else}checked="checked"{/if} onclick="return showBackground('pattern', '{$name|escape:'html':'UTF-8'}')" name="type_image_{$name|escape:'html':'UTF-8'}" value="pattern"/><label class="radioCheck"> {l s='Pattern default' mod='wtthemeconfigurator'}</label>
				<input type="radio"{if isset($options.$type_image) && $options.$type_image == 'file'}checked="checked"{/if}  onclick="return showBackground('file', '{$name|escape:'html':'UTF-8'}')" name="type_image_{$name|escape:'html':'UTF-8'}" value="file"/><label class="radioCheck" for="file"> {l s='Choose Image' mod='wtthemeconfigurator'}</label>
				</div>
				<div class="form-group">			
				<div id="image_pattern_{$name|escape:'html':'UTF-8'}" class="fimage" {if isset($options.$type_image) && $options.$type_image != 'pattern'}style="display:none" {/if}>
					<span class="bkg_pattern" style="background:url({$path_img|escape:'html':'UTF-8'}patterns/no_img.jpg) repeat;float:left;margin:0 10px 10px 0; width:30px; height:30px">
						<input type="radio" name="pattern_{$name|escape:'html':'UTF-8'}" value="no_img.jpg" {if !isset($options.$bg_img) || $options.$bg_img == 'no_img.jpg'}checked="checked"{/if}/>	
					</span>
					{foreach from=$pattern_list item = pattern key = k}				
						<span class="bkg_pattern" style="background:url({$pattern|escape:'html':'UTF-8'}) repeat;float:left;margin:0 10px 10px 0; width:30px; height:30px">
							<input type="radio" name="pattern_{$name|escape:'html':'UTF-8'}" value="{$k|intval}" {if isset($options.$bg_img) && isset($options.$type_image) && $options.$type_image == 'pattern' && $options.$bg_img == $k}checked="checked"{/if}/>	
						</span>
					{/foreach}
				</div>
				<div id="image_file_{$name|escape:'html':'UTF-8'}" class="fimage" {if isset($options.$type_image) && $options.$type_image != 'file'}style="display:none" {/if}>
					<div class="col-lg-9">
						<input id="{$name|escape:'html':'UTF-8'}" type="file" name="file_{$name|escape:'html':'UTF-8'}" class="hide"/>
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="{$name|escape:'html':'UTF-8'}-name" type="text" class="disabled" name="file_{$name|escape:'html':'UTF-8'}" readonly />
							<span class="input-group-btn">
								<button id="{$name|escape:'html':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='wtthemeconfigurator'}
								</button>
							</span>
						</div>
					</div>
					{if isset($options.$bg_img) && isset($options.$type_image) && $options.$type_image == 'file'}
					<div class="col-lg-3">
						<span><img src = "{$path_img|escape:'html':'UTF-8'}backgrounds/{$options.$bg_img|escape:'html':'UTF-8'}" width=30 height=30 /></span>
					</div>
					{/if}
				</div>
				<script type="text/javascript">
					
					$(document).ready(function(){
						$('#{$name|escape:'html':'UTF-8'}-selectbutton').click(function(e){
							$('#{$name|escape:'html':'UTF-8'}').trigger('click');
						});
						$('#{$name|escape:'html':'UTF-8'}').change(function(e){
							var val = $(this).val();
							var file = val.split(/[\\/]/);
							$('#{$name|escape:'html':'UTF-8'}-name').val(file[file.length-1]);
						});
					});
				</script>
				</div>
				<div class="form-group">							
					<input type="radio" {if isset($options.$repeat) && $options.$repeat == 'repeat'}checked="checked"{/if} name="background_repeat_{$name|escape:'html':'UTF-8'}" value="repeat"/><label class="radioCheck"> {l s='Repeat' mod='wtthemeconfigurator'}</label>
					<input type="radio"{if isset($options.$repeat) && $options.$repeat == 'repeat-x'}checked="checked"{/if}  name="background_repeat_{$name|escape:'html':'UTF-8'}" value="repeat-x"/><label class="radioCheck"> {l s='Repeat-x' mod='wtthemeconfigurator'}</label>
					<input type="radio"{if isset($options.$repeat) && $options.$repeat == 'repeat-y'}checked="checked"{/if}  name="background_repeat_{$name|escape:'html':'UTF-8'}" value="repeat-y"/><label class="radioCheck"> {l s='Repeat-y' mod='wtthemeconfigurator'}</label>
					<input type="radio"{if isset($options.$repeat) && $options.$repeat == 'no-repeat'}checked="checked"{/if}  name="background_repeat_{$name|escape:'html':'UTF-8'}" value="no-repeat"/><label class="radioCheck"> {l s='No repeat' mod='wtthemeconfigurator'}</label>
				</div>
				{if isset($list.note)}<p class="help-block" style="font-size:11px">{$list.note|escape:'html':'UTF-8'}</p>{/if}
			</div>
		</div>
		</div>
		{/foreach}
		
		<div class="form-group">
			<label for="parallax" class="control-label col-lg-3 ">
				{l s='Parallax Effects' mod='wtthemeconfigurator'}
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="input-group col-lg-2">
						<span class="switch prestashop-switch">
							<input type="radio" name="parallax" id="parallax_on" value="1" {if isset($options.parallax) && $options.parallax == 1} checked="checked" {/if}>
							<label for="parallax_on">Yes</label>
							<input type="radio" name="parallax" id="parallax_off" value="0" {if isset($options.parallax) && $options.parallax == 0} checked="checked" {/if}>
							<label for="parallax_off">No</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<label for="cpanel" class="control-label col-lg-3 ">
				{l s='Show Demo Frontend' mod='wtthemeconfigurator'}
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="input-group col-lg-2">
						<span class="switch prestashop-switch">
							<input type="radio" name="cpanel" id="cpanel_on" value="1" {if isset($options.cpanel) && $options.cpanel == 1} checked="checked" {/if}>
							<label for="cpanel_on">Yes</label>
							<input type="radio" name="cpanel" id="cpanel_off" value="0" {if isset($options.cpanel) && $options.cpanel == 0} checked="checked" {/if}>
							<label for="cpanel_off">No</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		
		{if $bg_colors != ''}<h3>{l s='Color' mod='wtthemeconfigurator'}</h3>{/if}
		{foreach from=$bg_colors item=list key=name}
		{assign var=bg_color  value=color_|cat:$name}		
		<div class="form-group">
		<label class="control-label col-lg-3">{$list.text|escape:'html':'UTF-8'}</label>
		<div class="col-lg-9">
			<div class="col-lg-5">
				<div class="input-group {$bg_color|escape:'html':'UTF-8'}">
					<input id="result_{$name|escape:'html':'UTF-8'}_color" type="text" name="color_{$name|escape:'html':'UTF-8'}" {if isset($options.$bg_color) &&  $options.$bg_color != ''}value="{$options.$bg_color|escape:'html':'UTF-8'}" style="background-color:{$options.$bg_color|escape:'html':'UTF-8'}"{/if}/>
					<span id="colobg_{$name|escape:'html':'UTF-8'}_color" class="input-group-btn" >
						<img src="{$smarty.const._PS_ADMIN_IMG_|escape:'html':'UTF-8'}color.png" style="cursor:pointer; margin-left:5px" />
					</span>
				</div>
				{if isset($list.note)}<p class="help-block" style="font-size:11px">{$list.note|escape:'html':'UTF-8'}</p>{/if}
				<script type="text/javascript">
					$(document).ready(function(){
						colorEvent("{$name|escape:'html':'UTF-8'}_color");
					});				
				</script>
			</div>
		</div>
		</div>
		{/foreach}
		{if $fonts != ''}<h3>{l s='Font' mod='wtthemeconfigurator'}</h3>{/if}
		{foreach from=$fonts item=list key=name}
		{assign var=font_family  value=font_family_|cat:$name}		
		<div class="form-group">
		<label class="control-label col-lg-3">{$list.text|escape:'html':'UTF-8'}</label>
		<div class="col-lg-9">
		<div class="col-lg-5">
			<select name="font_family_{$name|escape:'html':'UTF-8'}" id="font_family_{$name|escape:'html':'UTF-8'}" onchange="showResultChooseFont('font_family_{$name|escape:'html':'UTF-8'}','font_result_{$name|escape:'html':'UTF-8'}')">
				{foreach from=$font_list item = font key = k}				
					{$font|escape:'quotes':'UTF-8'}
				{/foreach}
			</select>
			{if isset($list.note)}<p class="help-block" style="font-size:11px">{$list.note|escape:'html':'UTF-8'}</p>{/if}
			<script type="text/javascript">	
				$(document).ready(function() {
					{if isset($options.$font_family) &&  $options.$font_family != ''}
						var f_m = '{$options.$font_family|escape:"html":"UTF-8"}';
						$("#font_family_{$name|escape:'html':'UTF-8'}").val(f_m);
					{else}
						$("#font_family_{$name|escape:'html':'UTF-8'}").val('');
					{/if}
				});
			</script>
		</div>
		<div class="col-lg-5"><span id="font_result_{$name|escape:'html':'UTF-8'}" {if isset($options.$font_family) &&  $options.$font_family != ''}style="font-family:{$options.$font_family|escape:'html':'UTF-8'}"{/if}>{if isset($options.$font_family) &&  $options.$font_family != ''}{$options.$font_family|escape:'html':'UTF-8'}{/if}</span></div>		
		</div>				
		</div>
		{if isset($options.$font_family) &&  $options.$font_family != ''}
		<script type="text/javascript">	
			$(document).ready(function() {
				$('head').append('<link id="link_' + '{$options.$font_family|escape:"html":"UTF-8"}' + '" rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' + '{$options.$font_family|escape:"html":"UTF-8"}' + '">');	
			});
		</script>
		{/if}
	{/foreach}	
	<div class="panel-footer">
		<button type="submit" value="1" id="resetSetting" name="resetSetting" class="btn btn-default pull-left" onclick="this.form.submit();">
			<i class="process-icon-reset"></i> {l s='Reset' mod='wtthemeconfigurator'}
		</button>
		<button type="submit" value="1" id="submit_color" name="{$submit_action|escape:'html':'UTF-8'}" class="btn btn-default pull-right" onclick="this.form.submit();">
			<i class="process-icon-save"></i> {l s='Save' mod='wtthemeconfigurator'}
		</button>
	</div>
</div>
</form>
<script type="text/javascript">	
	function showBackground(classActive, name)
	{
		$(".fimage").hide();
		$("#image_" + classActive + "_" + name).show("slow");
	}
	function showResultChooseFont(id,id_result)
	{
		$('link#link_' + id).remove();
		if($("#" + id).val() != "")
			$('head').append('<link id="link_' + id + '" rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' + $("#" + id).val() + '">');
		$("#" + id_result).html("" + $("#" + id).val() + "");
		$("#" + id_result).css("font-family","" + $("#" + id).val() + "");
	}
	function noteCustomer(thisForm)
	{
		 if (confirm("Do you really want to change the layout?") == true) {
		     thisForm.form.submit();
			return true;
		} else {
			return false;
		}
	}
</script>
