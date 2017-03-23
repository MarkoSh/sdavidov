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

class BlogPostCategory extends ObjectModel
{
	public $id_wt_blog_post_category;
	public $id_author;
	public static $definition = array(
	'table' => 'wt_blog_post_category',
	'primary' => 'id_wt_blog_post_category',
	'multilang'=>false,
	'fields' => array(
		'id_wt_blog_post_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		'id_wt_blog_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt')
	),
	);

	public static function getToltalByCategory($id_lang, $id_category, $limit_start, $limit)
	{
	$result = array();
	$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post_lang pl, '._DB_PREFIX_.'wt_blog_post p 
			WHERE pl.id_lang='.$id_lang.' and p.active = 1 AND pl.id_wt_blog_post=p.id_wt_blog_post AND p.id_category = '.$id_category.'
			ORDER BY p.id_wt_blog_post DESC LIMIT '.$limit_start.','.$limit;

	if (!$posts = Db::getInstance()->executeS($sql))
		return false;

		$i = 0;
		$BlogCategory = new BlogCategory();
		foreach ($posts as $post)
		{
			$result[$i]['id_post'] = $post['id_wt_blog_post'];
			$result[$i]['viewed'] = $post['viewed'];
			$result[$i]['meta_title'] = $post['meta_title'];
			$result[$i]['meta_description'] = $post['meta_description'];
			$result[$i]['short_description'] = $post['short_description'];
			$result[$i]['content'] = $post['content'];
			$result[$i]['meta_keyword'] = $post['meta_keyword'];
			$result[$i]['id_category'] = $post['id_category'];
			$result[$i]['link_rewrite'] = $post['link_rewrite'];
			$result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
			$employee = new Employee( $post['id_author']);
			$result[$i]['lastname'] = $employee->lastname;
			$result[$i]['firstname'] = $employee->firstname;
			if (file_exists(_PS_MODULE_DIR_.'wtblog/views/img/'.$post['id_wt_blog_post'].'.jpg'))
			{
				$image = $post['id_wt_blog_post'];
				$result[$i]['post_img'] = $image;
			}
			else
				$result[$i]['post_img'] = 'no';
			$result[$i]['created'] = $post['created'];
			$i++;
		}
	return $result;
	}  
}