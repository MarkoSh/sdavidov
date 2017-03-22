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
<div class="blog-detail">
{capture name=path}<a href="{wtblog::GetSmartBlogLink('wtblog')|escape:'html':'UTF-8'}">{l s='All Blog News' mod='wtblog'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{$meta_title|escape:'html':'UTF-8'}{/capture}
<div id="content" class="block">
   <div id="blog_article" class="blog-post">
		<div class="lipsum" class="articleContent">
			{assign var="activeimgincat" value='0'}
			{$activeimgincat = $wtshownoimg} 
			{if ($post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
				<a id="post_images" href="javascript:void(0)">
					<img src="{$modules_dir|escape:'html':'UTF-8'}wtblog/views/img/{$post_img|escape:'html':'UTF-8'}-single-default.jpg" alt="{$meta_title|escape:'html':'UTF-8'}"/>
				</a>
			{/if}
		</div>
		<div class="blog-tool">
			{assign var="catOptions" value=null}
			{$catOptions.id_category = $id_category}
			{$catOptions.slug = $cat_link_rewrite}
			<span class="date-time">{$created|date_format|escape:'html':'UTF-8'}</span>
			<span class="comment">
				<i class="icon icon-comments"></i>
				{if $countcomment != ''}{$countcomment|escape:'html':'UTF-8'}{else}{l s='0' mod='wtblog'}{/if}{l s=' Comments' mod='wtblog'}
			</span>
			{if $wtshowauthor ==1}
			<span class="author">
				{l s='Posted by ' mod='wtblog'}
				<i class="icon icon-user"></i>
				<span itemprop="author">{if $wtshowauthorstyle != 0}{$firstname|escape:'html':'UTF-8'} {$lastname|escape:'html':'UTF-8'}{else}{$lastname|escape:'html':'UTF-8'} {$firstname|escape:'html':'UTF-8'}{/if}</span>
			</span>
			{/if}
			<span class="articleSection">
				<a href="{wtblog::GetSmartBlogLink('wtblog_category',$catOptions)|escape:'html':'UTF-8'}">{$title_category|escape:'html':'UTF-8'}</a>
			</span>
		</div>
		<div class="page-item-title">
			<h1>{$meta_title|escape:'html':'UTF-8'}</h1>
		</div>
		<div class="article-desc">
		   {$content|escape:'quotes':'UTF-8'|replace:"\'":"'"}
		</div>
		<div class="blog-content-bottom">
			{if $tags != ''}
				<div class="tags-update">
					<span class="tags">
						<i class="icon-tag"></i>
						<strong>{l s='Tags:' mod='wtblog'} </strong> 
						{foreach from=$tags item=tag}
							{assign var="options" value=null}
							{$options.tag = $tag.name}
							<a title="tag" href="{wtblog::GetSmartBlogLink('wtblog_tag',$options)|escape:'html':'UTF-8'|escape:'html':'UTF-8'}">{$tag.name|escape:'html':'UTF-8'}</a>,
						{/foreach}
					</span>
				</div>
		   {/if}
		   <div class="blog-social">
				<p class="wtblog_product list-inline no-print">
					<button data-type="twitter" type="button" class="btn btn-default btn-twitter social-sharing">
						<i class="icon-twitter"></i> {l s='Tweet' mod='wtblog'}
					</button>
					<button data-type="facebook" type="button" class="btn btn-default btn-facebook social-sharing">
						<i class="icon-facebook"></i> {l s='Share' mod='wtblog'}
					</button>
					<button data-type="google-plus" type="button" class="btn btn-default btn-google-plus social-sharing">
						<i class="icon-google-plus"></i> {l s='Google+' mod='wtblog'}
					</button>
					<button data-type="pinterest" type="button" class="btn btn-default btn-pinterest social-sharing">
						<i class="icon-pinterest"></i> {l s='Pinterest' mod='wtblog'}
					</button>
				</p>
		   </div>
	   </div>
   </div>
</div>
<div class="blog-extra">
	<div class="post-related">
		<h3 class="title_block">{l s='Related Articles' mod='wtblog'}</h3>
		<ul class="releted">
			{foreach from=$post_related item="post"}
				{assign var="options" value=null}
				{$options.id_post= $post.id_wt_blog_post}
				{$options.slug= $post.link_rewrite}
				<li>
				   <a class="title paddleftreleted"  title="{$post.meta_title|escape:'html':'UTF-8'}" href="{wtblog::GetSmartBlogLink('wtblog_post',$options)|escape:'html':'UTF-8'}">{$post.meta_title|escape:'html':'UTF-8'}</a>
				   <span class="info">{$post.created|date_format:"%b %d, %Y"|escape:'html':'UTF-8'}</span>
				</li> 
			{/foreach}
		</ul>
	</div>
{if $countcomment != ''}
	<div id="article_comments">
		<h3 class="title_block">{l s=' Comments' mod='wtblog'} ({if $countcomment != ''}{$countcomment|escape:'html':'UTF-8'}{else}0{/if})</h3>
		<div id="comments">      
			{$i=1}
			{foreach from=$comments item=comment}
				{include file="./comment_loop.tpl" childcommnets=$comment}
			{/foreach}
		</div>
	</div>
{/if}
{if Configuration::get('wtenablecomment') == 1}
{if $comment_status == 1}
	<div class="wtblogcomments" id="respond">
		<h4 class="comment-reply-title" id="reply-title">{l s='Leave a Reply'  mod='wtblog'}
			<small style="float:right;">
				<a style="display: none;" href="/wp/sellya/sellya/this-is-a-post-with-preview-image/#respond" id="cancel-comment-reply-link" rel="nofollow">{l s='Cancel Reply'  mod='wtblog'}</a>
			</small>
		</h4>
		<div id="commentInput">
			<form action="" method="post" id="commentform">
				<table>
				<tbody>
				<tr>
					<td><span class="required">*</span> <strong>{l s='Name:'  mod='wtblog'} </strong></td>
					<td><input type="text" tabindex="1" class="inputName form-control grey" value="" name="name"></td>
				</tr>
				<tr>
					<td><span class="required">*</span> <strong>{l s='E-mail:'  mod='wtblog'} </strong><span class="note">{l s='(Not Published)'  mod='wtblog'}</span></td>
					<td>
					<input type="email" tabindex="2" class="inputMail form-control grey" value="" name="mail">
					</td>
				</tr>
				<tr>
					<td><strong>{l s='Website:'  mod='wtblog'}</strong><span class="note"> {l s='(Site url with'  mod='wtblog'} http://)</span></td>
				<td><input type="text" tabindex="3" value="" name="website" class="form-control grey"></td>
				</tr>
				<tr>
					<td><span class="required">*</span> <strong> {l s='Comment:'  mod='wtblog'}</strong></td>
					<td>
					<textarea tabindex="4" class="inputContent form-control grey" rows="8" cols="50" name="comment"></textarea>
					</td>
				</tr>
				{if Configuration::get('wtcaptchaoption') == '1'}
				<tr>
					<td></td>
					<td><img src="{$modules_dir|escape:'html':'UTF-8'}wtblog/classes/CaptchaSecurityImages.php?width=120&height=40&characters=5" alt=""/></td>
				</tr>
				<tr>
					<td><strong>{l s='Type Code' mod='wtblog'}</strong></td>
					<td><input type="text" tabindex="" value="" name="wtblogcaptcha" class="wtblogcaptcha form-control grey"></td>
				</tr>
				{/if}
				</tbody>
				</table>
				<input type='hidden' name='comment_post_ID' value='1478' id='comment_post_ID' />
				<input type='hidden' name='id_post' value='{$id_post|intval}' id='id_post' />
				<input type='hidden' name='comment_parent' id='comment_parent' value='0' />
				<div class="right">
					<div class="submit">
						<input type="submit" name="addComment" id="submitComment" class="bbutton btn btn-default button-medium" value="Submit">
					</div>
				</div>
			</form>	
		</div>
	</div>
</div>
<script type="text/javascript">
$('#submitComment').bind('click',function(event)
{
	event.preventDefault();
	var data = { 'action':'postcomment', 
	'id_post':$('input[name=\'id_post\']').val(),
	'comment_parent':$('input[name=\'comment_parent\']').val(),
	'name':$('input[name=\'name\']').val(),
	'website':$('input[name=\'website\']').val(),
	'wtblogcaptcha':$('input[name=\'wtblogcaptcha\']').val(),
	'comment':$('textarea[name=\'comment\']').val(),
	'mail':$('input[name=\'mail\']').val() };
	$.ajax( {
	  url: baseDir + 'modules/wtblog/ajax.php',
	  data: data,
	  dataType: 'json',
	  beforeSend: function()
		{
			$('.success, .warning, .error').remove();
			$('#submitComment').attr('disabled', true);
			$('#commentInput').before('<div class="attention"><img src="{$modules_dir|escape:"html":"UTF-8"}wtblog/views/img/loading.gif" alt="" />Please wait!</div>');
		},
		complete: function()
		{
			$('#submitComment').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(json)
		{
			if (json['error']) {
				$('#commentInput').before('<div class="warning">' + '<i class="icon-warning-sign icon-lg"></i>' + json['error']['common'] + '</div>');
				
				if (json['error']['name']) {
					$('.inputName').after('<span class="error">' + json['error']['name'] + '</span>');
				}
				if (json['error']['mail']) {
					$('.inputMail').after('<span class="error">' + json['error']['mail'] + '</span>');
				}
				if (json['error']['comment']) {
					$('.inputContent').after('<span class="error">' + json['error']['comment'] + '</span>');
				}
				if (json['error']['captcha']) {
					$('.wtblogcaptcha').after('<span class="error">' + json['error']['captcha'] + '</span>');
				}
			}
			
			if (json['success'])
			{
				$('input[name=\'name\']').val('');
				$('input[name=\'mail\']').val('');
				$('input[name=\'website\']').val('');
				$('textarea[name=\'comment\']').val('');
				$('input[name=\'wtblogcaptcha\']').val('');
			
				$('#commentInput').before('<div class="success">' + json['success'] + '</div>');
				setTimeout(function(){
					$('.success').fadeOut(300).delay(450).remove();
					},2500);
				location.reload();
			}
		}
	} );
});	
    var addComment = {
	moveForm : function(commId, parentId, respondId, postId) {

		var t = this, div, comm = t.I(commId), respond = t.I(respondId), cancel = t.I('cancel-comment-reply-link'), parent = t.I('comment_parent'), post = t.I('comment_post_ID');

		if ( ! comm || ! respond || ! cancel || ! parent )
			return;
 
		t.respondId = respondId;
		postId = postId || false;

		if ( ! t.I('wp-temp-form-div') ) {
			div = document.createElement('div');
			div.id = 'wp-temp-form-div';
			div.style.display = 'none';
			respond.parentNode.insertBefore(div, respond);
		}

		comm.parentNode.insertBefore(respond, comm.nextSibling);
		if ( post && postId )
			post.value = postId;
		parent.value = parentId;
		cancel.style.display = '';

		cancel.onclick = function() {
			var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);

			if ( ! temp || ! respond )
				return;

			t.I('comment_parent').value = '0';
			temp.parentNode.insertBefore(respond, temp);
			temp.parentNode.removeChild(temp);
			this.style.display = 'none';
			this.onclick = null;
			return false;
		};

		try { t.I('comment').focus(); }
		catch(e) {}

		return false;
	},

	I : function(e) {
		return document.getElementById(e);
	}
};

</script>
{/if}
{/if}
</div>
