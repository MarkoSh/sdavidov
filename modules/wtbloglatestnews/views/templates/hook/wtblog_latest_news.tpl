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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="blog_latest_new_home" class="block col-xs-12 col-sm-3">
    <p class='title_block'><a href="{wtblog::GetSmartBlogLink('wtblog')|escape:'html':'UTF-8'}">
	{l s='From Out Blog' mod='wtbloglatestnews'}</a></p>
    <div class="block_content">
        {if isset($view_data) AND !empty($view_data)}
            {assign var='i' value=1}
			<ul>
            {foreach from=$view_data item=post}
                    {assign var="options" value=null}
                    {$options.id_post = $post.id}
                    {$options.slug = $post.link_rewrite}
                    <li>
						<h5 class="post_title"><a href="{wtblog::GetSmartBlogLink('wtblog_post',$options)|escape:'html':'UTF-8'}">{$post.title|escape:'html':'UTF-8'}</a></h5>
                        <div class="blog-date">{$post.date_added|escape:'htmlall':'UTF-8'}</div>
                        <p>
                            {$post.short_description|escape:'html':'UTF-8'|strip_tags|truncate:90:'...'}
                        </p>
                        <a href="{wtblog::GetSmartBlogLink('wtblog_post',$options)|escape:'html':'UTF-8'}"  class="r_more">{l s='Read More' mod='wtbloglatestnews'}</a>
                    </li>
                {$i=$i+1}
            {/foreach}
			</ul>
        {/if}
     </div>
</div>