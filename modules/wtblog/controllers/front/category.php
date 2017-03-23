<?php
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
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class WtBlogCategoryModuleFrontController extends wtblogModuleFrontController
{
	public $ssl = true;
	public $wtblogCategory;

	public function init()
	{
		parent::init();
	}
	public function initContent()
	{
		parent::initContent();
		$category_status = '';
		$totalpages = '';
		$cat_image = 'no';
		$categoryinfo = '';
		$title_category = '';
		$cat_link_rewrite = '';
		$allNews = array();
		$blogcomment = new Blogcomment();
		$WtBlogPost = new WtBlogPost();
		$BlogCategory = new BlogCategory();
		$BlogPostCategory = new BlogPostCategory();
		$id_category = Tools::getvalue('id_category');
		$posts_per_page = Configuration::get('wtpostperpage');
		if ($posts_per_page == 0)
			$posts_per_page = 1;
		$limit_start = 0;
		$limit = $posts_per_page;
		if (!$id_category = Tools::getvalue('id_category'))
			$total = $WtBlogPost->getToltal($this->context->language->id);
		else
		{
			$total = $WtBlogPost->getToltalByCategory($this->context->language->id, $id_category);
			Hook::exec('actionsbcat', array('id_category' => Tools::getvalue('id_category')));
		}
		if ($total != '' || $total != 0)
			$totalpages = ceil($total / $posts_per_page);
		if ((boolean)Tools::getValue('page'))
		{
			$c = Tools::getValue('page');
			$limit_start = $posts_per_page * ($c - 1);
		}
		if (!$id_category = Tools::getvalue('id_category'))
			$allNews = $WtBlogPost->getAllPost($this->context->language->id, $limit_start, $limit);
		else
		{
			if (file_exists(_PS_MODULE_DIR_.'wtblog/views/img/category/'.$id_category.'.jpg'))
				$cat_image = $id_category;
			else
				$cat_image = 'no';
			$categoryinfo = $BlogCategory->getNameCategory($id_category);
			$title_category = $categoryinfo[0]['meta_title'];
			$category_status = $categoryinfo[0]['active'];
			$cat_link_rewrite = $categoryinfo[0]['link_rewrite'];
			if ($category_status == 1)
				$allNews = $BlogPostCategory->getToltalByCategory($this->context->language->id, $id_category, $limit_start, $limit);
			elseif ($category_status == 0)
				$allNews = '';
		}
		$i = 0;
		$to = array();
		if (!empty($allNews))
		{
			foreach ($allNews as $item)
			{
				$to[$i] = $blogcomment->getToltalComment($item['id_post']);
				$i++;
			}
			$j = 0;
			foreach ($to as $item)
			{
				if ($item == '')
					$allNews[$j]['totalcomment'] = 0;
				else
					$allNews[$j]['totalcomment'] = $item;
				$j++;
			}
		}
		$this->context->smarty->assign(
		array(
			'postcategory'=> $allNews,
			'category_status'=>$category_status,
			'title_category'=>$title_category,
			'cat_link_rewrite'=>$cat_link_rewrite,
			'id_category'=>$id_category,
			'cat_image'=>$cat_image,
			'categoryinfo'=>$categoryinfo,
			'wtshowauthorstyle'=>Configuration::get('wtshowauthorstyle'),
			'wtshowauthor'=>Configuration::get('wtshowauthor'),
			'limit'=>isset($limit) ? $limit : 0,
			'limit_start'=>isset($limit_start) ? $limit_start : 0 ,
			'c'=>isset($c) ? $c : 1,
			'total'=>$total,
			'wtblogliststyle' => Configuration::get('wtblogliststyle'),
			'wtcustomcss' => Configuration::get('wtcustomcss'),
			'wtshownoimg' => Configuration::get('wtshownoimg'),
			'wtdisablecatimg' => Configuration::get('wtdisablecatimg'),
			'wtshowviewed' => Configuration::get('wtshowviewed'),
			'post_per_page'=>$posts_per_page,
			'pagenums' => $totalpages - 1,
			'totalpages' =>$totalpages
			));
			
		$template_name  = 'postcategory.tpl';
		$this->setTemplate($template_name );
	}
}