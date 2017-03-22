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

{if $comment.id_wt_blog_comment != ''}
<ul class="comment-list">
    <li id="comment-{$comment.id_wt_blog_comment|intval}">
		<div class="comment-content">
			<p>{$childcommnets.content|escape:'html':'UTF-8'}</p>
			<div class="blog-tool">
				<div class="name">{$childcommnets.name|escape:'html':'UTF-8'}</div>
				<div class="created">
					<span itemprop="commentTime">{$childcommnets.created|date_format|escape:'html':'UTF-8'}</span>
				</div>
			</div>
			{if Configuration::get('wtenablecomment') == 1}
				{if $comment_status == 1}
					<div class="reply">
						   <a onclick="return addComment.moveForm('comment-{$comment.id_wt_blog_comment|escape:'html':'UTF-8'}', '{$comment.id_wt_blog_comment|escape:'html':'UTF-8'}', 'respond', '{$smarty.get.id_post|intval}')"  class="comment-reply-link">
						   {l s='Reply' mod='wtblog'}</a>
					 </div>
				{/if}
			{/if}
		</div>
		{if isset($childcommnets.child_comments)}
			{foreach from=$childcommnets.child_comments item=comment}
			   {if isset($childcommnets.child_comments)}
				{include file="./comment_loop.tpl" childcommnets=$comment}
				{$i=$i+1}
				{/if}
			{/foreach}
		 {/if}
    </li>
</ul>
{/if}