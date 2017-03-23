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
{assign var="options" value=null}
{$options.id_post = $post.id_post} 
{$options.slug = $post.link_rewrite}
{assign var="options" value=null}
{$options.id_post = $post.id_post}
{$options.slug = $post.link_rewrite}
{assign var="catlink" value=null}
{$catlink.id_category = $post.id_category}
{$catlink.slug = $post.cat_link_rewrite}
<div id="wtblogpost-{$post.id_post|intval}" class="wtblogpost">
	<div class="article-content">
		<a itemprop="url" href="{wtblog::GetSmartBlogLink('wtblog_post',$options)|escape:'html':'UTF-8'}" title="{$post.meta_title|escape:'html':'UTF-8'}" class="imageFeaturedLink">
			{assign var="activeimgincat" value='0'}
			{$activeimgincat = $wtshownoimg} 
			{if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
				<img itemprop="image" alt="{$post.meta_title|escape:'html':'UTF-8'}" src="{$modules_dir|escape:'html':'UTF-8'}wtblog/views/img/{$post.post_img|escape:'html':'UTF-8'}-home-default.jpg" class="imageFeatured"/>
			{/if}
		</a>
	</div>
	<div class="article-header">
		<div class="blog-tool">
			<span class="date-time">
				{$post.created|date_format|escape:'html':'UTF-8'} 
			</span>
			<span class="comment">
				<i class="icon icon-comment-o"></i>
				<a title="{$post.totalcomment|escape:'html':'UTF-8'} Comments" href="{wtblog::GetSmartBlogLink('wtblog_post',$options)|escape:'html':'UTF-8'}#articleComments">{$post.totalcomment|escape:'html':'UTF-8'} {l s=' Comments' mod='wtblog'}</a>
			</span>
			{if $wtshowauthor ==1}
			<span>
				{l s='Posted by' mod='wtblog'}	
				<span itemprop="author">
					<i class="icon icon-user"></i>
					{if $wtshowauthorstyle != 0}
						{$post.firstname|escape:'html':'UTF-8'} {$post.lastname|escape:'html':'UTF-8'}
					{else}
						{$post.lastname|escape:'html':'UTF-8'} {$post.firstname|escape:'html':'UTF-8'}
					{/if}
				</span>
			</span>
			{/if}
			<span>
				<i class="icon icon-tags"></i>
				<span itemprop="articleSection">
					<a href="{wtblog::GetSmartBlogLink('wtblog_category',$catlink)|escape:'html':'UTF-8'}">
					{if $title_category != ''}{$title_category|escape:'html':'UTF-8'}{else}{$post.cat_name|escape:'html':'UTF-8'}{/if}
					</a>
				</span>
			</span>
			{if $wtshowviewed ==1}
			<span>
				<i class="icon icon-eye-open"></i>
					{l s=' views' mod='wtblog'} ({$post.viewed|intval})
			</span>
			{/if}
		</div>
		<h5 class='blog-title'>
			<a title="{$post.meta_title|escape:'html':'UTF-8'}" href="{wtblog::GetSmartBlogLink('wtblog_post',$options)|escape:'html':'UTF-8'}">{$post.meta_title|truncate:42:''|escape:'html':'UTF-8'}</a>
		</h5>
	</div>
	 <div class="sdsarticle-des">
		<div itemprop="description" class="clearfix">
			<div class="lipsum">{$post.short_description|escape:'html':'UTF-8'}</div>
		</div>
	 </div>
	<div class="sdsreadMore">
	{assign var="options" value=null}
		{$options.id_post = $post.id_post}  
		{$options.slug = $post.link_rewrite}  
		 <span class="more">
			<a title="{$post.meta_title|escape:'html':'UTF-8'}" href="{wtblog::GetSmartBlogLink('wtblog_post',$options)|escape:'html':'UTF-8'}" class="r_more">
				{l s='Read more' mod='wtblog'}
			</a>
		</span>
	</div>
</div>