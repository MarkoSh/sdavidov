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

{capture name=path}<a href="{wtblog::GetSmartBlogLink('wtblog')|escape:'html':'UTF-8'}">{l s='All Blog News' mod='wtblog'}</a>
    {if $title_category != ''}
    <span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{$title_category|escape:'html':'UTF-8'}{/if}{/capture}
    {if $postcategory == ''}
        {if $title_category != ''}
             <p class="error">{l s='No Post in Category' mod='wtblog'}</p>
        {else}
             <p class="error">{l s='No Post in Blog' mod='wtblog'}</p>
        {/if}
    {else}
	{if $wtdisablecatimg == '1'}
                  {assign var="activeimgincat" value='0'}
                    {$activeimgincat = $wtshownoimg} 
        {if $title_category != ''}        
           {foreach from=$categoryinfo item=category}
            <div id="sdsblogCategory">
               {if ($cat_image != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                   <img alt="{$category.meta_title|escape:'html':'UTF-8'}" src="{$modules_dir|escape:'html':'UTF-8'}/wtblog/views/img/category/{$cat_image|escape:'html':'UTF-8'}-home-default.jpg" class="imageFeatured">
               {/if}
                {$category.description|escape:'quotes':'UTF-8'}
            </div>
             {/foreach}  
        {/if}
    {/if}
    <div id="wtblogcat" class="block clearfix">
{foreach from=$postcategory item=post name=postcategory}
	<div class="blog-item{if $smarty.foreach.postcategory.iteration%2 == 0} even{else} odd{/if}">
		{include file="./category_loop.tpl" postcategory=$postcategory}
	</div>
{/foreach}
    </div>
    {if !empty($pagenums)}
	<div id="pagination_bottom" class="pagination-bottom-blog clearfix">
		<ul class="pagination-blog">
			{for $k=0 to $pagenums}
				{if $title_category != ''}
					{assign var="options" value=null}
					{$options.page = $k+1}
					{$options.id_category = $id_category}
					{$options.slug = $cat_link_rewrite}
				{else}
					{assign var="options" value=null}
					{$options.page = $k+1}
				{/if}
				{if ($k+1) == $c}
					<li class="active current"><span>{$k+1|intval}</span></li>
				{else}
						{if $title_category != ''}
							<li><a class="page-link" href="{wtblog::GetSmartBlogLink('wtblog_category_pagination',$options)|escape:'html':'UTF-8'}"><span>{$k+1|intval}</span></a></li>
						{else}
							<li><a class="page-link" href="{wtblog::GetSmartBlogLink('wtblog_list_pagination',$options)|escape:'html':'UTF-8'}"><span>{$k+1|intval}</span></a></li>
						{/if}
				{/if}
		   {/for}
		</ul>
	</div>
 {/if}
 {/if}