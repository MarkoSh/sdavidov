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

class WtBlogPost extends ObjectModel
{
	public $id_wt_blog_post;
	public $id_author;
	public $id_category;
	public $position = 0;
	public $active = 1;
	public $available;
	public $created;
	public $modified;
	public $short_description;
	public $viewed;
	public $comment_status = 1;
	public $post_type;
	public $meta_title;
	public $meta_keyword;
	public $meta_description;
	public $image;
	public $content;
	public $link_rewrite;
	public $is_featured;

	public static $definition = array(
	'table' => 'wt_blog_post',
	'primary' => 'id_wt_blog_post',
	'multilang'=>true,
	'fields' => array(
		'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		'position' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		'id_author' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		'available' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		'created' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
		'modified' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
		'viewed' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		'is_featured' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		'comment_status' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		'post_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
		'image' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
		'meta_title' => array('type' => self::TYPE_STRING, 'validate' => 'isString','lang'=>true,'required'=>true),
		'meta_keyword' => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isString'),
		'meta_description' => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isString'),
		'short_description' => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isString','required'=>true),
		'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString','required'=>true),
		'link_rewrite' => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isString','required'=>false)
		)
	);
	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		Shop::addTableAssociation('wt_blog_post', array('type' => 'shop'));
			parent::__construct($id, $id_lang, $id_shop);
	}
	public static function getPost($id_post, $id_lang = null)
	{
		$result = array();
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post 
				WHERE pl.id_lang='.$id_lang.'
				AND p.active= 1 AND p.id_wt_blog_post = '.$id_post;
		if (!$post = Db::getInstance()->executeS($sql))
			return false;
				$result['id_post'] = $post[0]['id_wt_blog_post'];
				$result['meta_title'] = $post[0]['meta_title'];
				$result['meta_description'] = $post[0]['meta_description'];
				$result['short_description'] = $post[0]['short_description'];
				$result['meta_keyword'] = $post[0]['meta_keyword'];
				$result['content'] = $post[0]['content'];
				$result['active'] = $post[0]['active'];
				$result['created'] = $post[0]['created'];
				$result['comment_status'] = $post[0]['comment_status'];
				$result['viewed'] = $post[0]['viewed'];
				$result['is_featured'] = $post[0]['is_featured'];
				$result['post_type'] = $post[0]['post_type'];
				$result['id_category'] = $post[0]['id_category'];
				$employee = new Employee($post[0]['id_author']);
				$result['lastname'] = $employee->lastname;
				$result['firstname'] = $employee->firstname;
				if (file_exists(_PS_MODULE_DIR_.'wtblog/views/img/'.$post[0]['id_wt_blog_post'].'.jpg'))
				{
					$image = $post[0]['id_wt_blog_post'].'.jpg';
					$result['post_img'] = $image;
				}
				else
					$result['post_img'] = null;
		return $result;
	}

	public static function getAllPost($id_lang = null, $limit_start, $limit)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		if ($limit_start == '')
			$limit_start = 0;
		if ($limit == '')
			$limit = 5;
		$result = array();
		$blog_category = '';
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
				WHERE pl.id_lang='.$id_lang.'
				AND p.active= 1 ORDER BY p.id_wt_blog_post DESC LIMIT '.$limit_start.','.$limit;
		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
				$blog_category = new BlogCategory();
		$i = 0;
		foreach ($posts as $post)
		{
			$result[$i]['id_post'] = $post['id_wt_blog_post'];
			$result[$i]['is_featured'] = $post['is_featured'];
			$result[$i]['viewed'] = $post['viewed'];
			$result[$i]['meta_title'] = $post['meta_title'];
			$result[$i]['meta_description'] = $post['meta_description'];
			$result[$i]['short_description'] = $post['short_description'];
			$result[$i]['content'] = $post['content'];
			$result[$i]['meta_keyword'] = $post['meta_keyword'];
			$result[$i]['id_category'] = $post['id_category'];
			$result[$i]['link_rewrite'] = $post['link_rewrite'];
			$result[$i]['cat_name'] = $blog_category->getCatName($post['id_category']);
			$result[$i]['cat_link_rewrite'] = $blog_category->getCatLinkRewrite($post['id_category']);
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

	public static function getToltal($id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN
				'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
				WHERE pl.id_lang='.$id_lang.'
				AND p.active= 1';
		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
		return count($posts);
	}
	public static function getToltalByCategory($id_lang = null, $id_category = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		if ($id_category == null)
			$id_category = 1;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN
				'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
				WHERE pl.id_lang='.$id_lang.'
				AND p.active= 1 AND p.id_category = '.$id_category;
		if (!$posts = Db::getInstance()->executeS($sql))
	return false;
		return count($posts);
	}

	public static function addTags($id_lang = null, $id_post, $tag_list, $separator = ',')
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		if (!Validate::isUnsignedId($id_lang))
			return false;

		if (!is_array($tag_list))
			$tag_list = array_filter(array_unique(array_map('trim', preg_split('#\\'.$separator.'#', $tag_list, null, PREG_SPLIT_NO_EMPTY))));

		$list = array();
		if (is_array($tag_list))
			foreach ($tag_list as $tag)
			{
				$id_tag = BlogTag::TagExists($tag, (int)$id_lang);
				if (!$id_tag)
				{
					$tag_obj = new BlogTag(null, $tag, (int)$id_lang);
					if (!Validate::isLoadedObject($tag_obj))
					{
							$tag_obj->name = $tag;
							$tag_obj->id_lang = (int)$id_lang;
							$tag_obj->add();
					}
					if (!in_array($tag_obj->id, $list))
							$list[] = $tag_obj->id;
				}
				else
				{
						if (!in_array($id_tag, $list))
							$list[] = $id_tag;
				}
			}
		$data = '';
		foreach ($list as $tag)
			$data .= '('.(int)$tag.','.(int)$id_post.'),';
		$data = rtrim($data, ',');
		return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'wt_blog_post_tag` (`id_tag`, `id_post`)
		VALUES '.$data);
	}
	public function add($autodate = true, $null_values = false)
	{
		if (!parent::add($autodate, $null_values))
			return false;
		elseif (Tools::getIsset('products'))
			return $this->setProducts(Tools::getValue('products'));
		return true;
	}
	public static function postViewed($id_post)
	{
		$sql = 'UPDATE '._DB_PREFIX_.'wt_blog_post as p SET p.viewed = (p.viewed+1) where p.id_wt_blog_post = '.$id_post;
		return Db::getInstance()->execute($sql);
	}
	public function setProducts($array)
	{
		$result = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'wt_blog_post_tag WHERE id_tag = '.(int)$this->id);
		if (is_array($array))
		{
			$array = array_map('intval', $array);
			$result &= ObjectModel::updateMultishopTable('wt_blog_post_tag', array('indexed' => 0), 'a.id_post IN ('.implode(',', $array).')');
			$ids = array();
			foreach ($array as $id_post)
				$ids[] = '('.(int)$id_post.','.(int)$this->id.')';

			if ($result)
			{
				$result &= Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'wt_blog_post_tag (id_post, id_tag) VALUES '.implode(',', $ids));
				if (Configuration::get('PS_SEARCH_INDEXATION'))
					$result &= Search::indexation(false);
			}
		}
		return $result;
	}
	public static function deleteTagsForProduct($id_post)
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'wt_blog_post_tag` WHERE `id_post` = '.(int)$id_post);
	}
	public static function getProductTags($id_post)
	{
		$id_lang = (int)Context::getContext()->language->id;
		if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT  t.`name`
		FROM '._DB_PREFIX_.'wt_blog_tag t
		LEFT JOIN '._DB_PREFIX_.'wt_blog_post_tag pt ON (pt.id_tag = t.id_tag AND t.id_lang = '.$id_lang.')
		WHERE pt.`id_post`='.(int)$id_post))
			return false;
		return $tmp;
	}
	public static function getProductTagsBylang($id_post, $id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		$tags = '';
			if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT  t.`name`
			FROM '._DB_PREFIX_.'wt_blog_tag t
			LEFT JOIN '._DB_PREFIX_.'wt_blog_post_tag pt ON (pt.id_tag = t.id_tag AND t.id_lang = '.$id_lang.')
			WHERE pt.`id_post`='.(int)$id_post))
					return false;
				$i = 1;
		foreach ($tmp as $val)
		{
			if ($i >= count($tmp))
				$tags .= $val['name'];
			else
				$tags .= $val['name'].',';
			$i++;
		}
		return $tags;
	}
	public static function getPopularPosts($id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		if (Configuration::get('wtshowpopularpost') != '' && Configuration::get('wtshowpopularpost') != null)
			$limit = Configuration::get('wtshowpopularpost');
		else
			$limit = 5;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT p.viewed ,p.created , p.id_wt_blog_post,pl.meta_title,pl.link_rewrite 
			FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
			WHERE pl.id_lang='.$id_lang.' AND p.active = 1 ORDER BY p.viewed DESC LIMIT 0,'.$limit);
		return $result;
	}
	public static function getRelatedPosts($id_lang = null, $id_cat = null, $id_post = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		if (Configuration::get('wtshowrelatedpost') != '' && Configuration::get('wtshowrelatedpost') != null)
			$limit = Configuration::get('wtshowrelatedpost');
		else
		{
			$limit = 5;
			if ($id_cat == null)
				$id_cat = 1;
		}
		if ($id_post == null)
			$id_post = 1;
			$sql = 'SELECT  p.id_wt_blog_post,p.created,pl.meta_title,pl.link_rewrite FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
				WHERE pl.id_lang='.$id_lang.'  AND p.active = 1 AND p.id_category = '.$id_cat.
				' AND p.id_wt_blog_post != '.$id_post.' ORDER BY p.id_wt_blog_post DESC LIMIT 0,'.$limit;

		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
		return $posts;
	}
	public static function getRecentPosts($id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		if (Configuration::get('wtshowrecentpost') != '' && Configuration::get('wtshowrecentpost') != null)
			$limit = Configuration::get('wtshowrecentpost');
		else
			$limit = 5;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT  p.id_wt_blog_post,p.created,pl.meta_title,pl.link_rewrite FROM '
				._DB_PREFIX_.'wt_blog_post p INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
				WHERE pl.id_lang='.$id_lang.'  AND p.active = 1 ORDER BY p.id_wt_blog_post DESC LIMIT 0,'.$limit);
		return $result;
	}

	public static function getArchiveResult($month = null, $year = null, $limit_start = 0, $limit = 5)
	{
		$blog_category = '';
		$result = array();
		$id_lang = (int)Context::getContext()->language->id;
		if ($month != '' && $month != null && $year != '' && $year != null)
		{
			$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post s INNER JOIN '._DB_PREFIX_.'wt_blog_post_lang sl ON s.id_wt_blog_post = sl.id_wt_blog_post
				 and sl.id_lang = '.$id_lang.' INNER JOIN '._DB_PREFIX_.'wt_blog_post_shop ps ON ps.id_wt_blog_post = s.id_wt_blog_post AND ps.id_shop = '
				.(int)Context::getContext()->shop->id.'
			where s.active = 1 and MONTH(s.created) = '.$month.' AND YEAR(s.created) = '.$year.' ORDER BY s.id_wt_blog_post DESC';
		}elseif ($month == '' && $month == null && $year != '' && $year != null)
		{
			$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post s INNER JOIN '._DB_PREFIX_.'wt_blog_post_lang sl ON s.id_wt_blog_post = sl.id_wt_blog_post
				 and sl.id_lang = '.$id_lang.' INNER JOIN '._DB_PREFIX_.'wt_blog_post_shop ps ON ps.id_wt_blog_post = s.id_wt_blog_post AND ps.id_shop = '
				.(int)Context::getContext()->shop->id.'
		where s.active = 1 AND YEAR(s.created) = '.$year.' ORDER BY s.id_wt_blog_post DESC';
		}
		elseif ($month != '' && $month != null && $year == '' && $year == null)
		{
			$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post s INNER JOIN '._DB_PREFIX_.'wt_blog_post_lang sl ON s.id_wt_blog_post = sl.id_wt_blog_post
				and sl.id_lang = '.$id_lang.' INNER JOIN '._DB_PREFIX_.'wt_blog_post_shop ps ON ps.id_wt_blog_post = s.id_wt_blog_post AND ps.id_shop = '
				.(int)Context::getContext()->shop->id.'
				where s.active = 1 AND  MONTH(s.created) = '.$month.'  ORDER BY s.id_wt_blog_post DESC';
		}
		else
		{
			$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post s INNER JOIN '._DB_PREFIX_.'wt_blog_post_lang sl ON s.id_wt_blog_post = sl.id_wt_blog_post
				and sl.id_lang = '.$id_lang.' INNER JOIN '._DB_PREFIX_.'wt_blog_post_shop ps ON ps.id_wt_blog_post = s.id_wt_blog_post AND ps.id_shop = '
				.(int)Context::getContext()->shop->id.'
			where s.active = 1 ORDER BY s.id_wt_blog_post DESC';
		}
		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
			$blog_category = new BlogCategory();
			$i = 0;
			foreach ($posts as $post)
			{
				$result[$i]['id_post'] = $post['id_wt_blog_post'];
				$result[$i]['viewed'] = $post['viewed'];
				$result[$i]['is_featured'] = $post['is_featured'];
				$result[$i]['meta_title'] = $post['meta_title'];
				$result[$i]['short_description'] = $post['short_description'];
				$result[$i]['meta_description'] = $post['meta_description'];
				$result[$i]['content'] = $post['content'];
				$result[$i]['meta_keyword'] = $post['meta_keyword'];
				$result[$i]['id_category'] = $post['id_category'];
				$result[$i]['link_rewrite'] = $post['link_rewrite'];
				$result[$i]['cat_name'] = $blog_category->getCatName($post['id_category']);
				$result[$i]['cat_link_rewrite'] = $blog_category->getCatLinkRewrite($post['id_category']);
				$employee = new Employee($post['id_author']);
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
	public static function getArchiveD($month, $year)
	{
		$sql = 'SELECT DAY(p.created) as day FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN '._DB_PREFIX_
		.'wt_blog_post_shop ps ON p.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '
		.(int)Context::getContext()->shop->id.' 
				 where MONTH(p.created) = '.$month.' AND YEAR(p.created) = '.$year.' GROUP BY DAY(p.created)';

		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
		return $posts;
	}

	public static function getArchiveM($year)
	{
		$sql = 'SELECT MONTH(p.created) as month FROM '._DB_PREFIX_.'wt_blog_post p  INNER JOIN '._DB_PREFIX_
		.'wt_blog_post_shop ps ON p.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '
		.(int)Context::getContext()->shop->id.' 
				 where YEAR(p.created) = '.$year.' GROUP BY MONTH(p.created)';
		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
		return $posts;
	}
	public static function getArchive()
	{
		$result = array();
		$sql = 'SELECT YEAR(p.created) as year FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN '._DB_PREFIX_
		.'wt_blog_post_shop ps ON p.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '
		.(int)Context::getContext()->shop->id.' 
			GROUP BY YEAR(p.created)';
		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
						$i = 0;
					foreach ($posts as $value)
					{
						$result[$i]['year'] = $value['year'];
						$result[$i]['month'] = WtBlogPost::getArchiveM($value['year']);
						$months = WtBlogPost::getArchiveM($value['year']);
						$j = 0;
						foreach ($months as $month)
						{
							$result[$i]['month'][$j]['day'] = WtBlogPost::getArchiveD($month['month'], $value['year']);
							$j++;
						}
						$i++;
					}
		return $result;
	}

	public static function SmartBlogSearchPost($keyword = null, $id_lang = null, $limit_start = 0, $limit = 5)
	{
		if ($keyword == null)
			return false;
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post_lang pl, '._DB_PREFIX_.'wt_blog_post p 
				WHERE pl.id_lang='.$id_lang.'  AND p.active = 1 
				AND pl.id_wt_blog_post=p.id_wt_blog_post AND
				(pl.meta_title LIKE \'%'.$keyword.'%\' OR
				 pl.meta_keyword LIKE \'%'.$keyword.'%\' OR
				 pl.meta_description LIKE \'%'.$keyword.'%\' OR
				 pl.content LIKE \'%'.$keyword.'%\') ORDER BY p.id_wt_blog_post DESC';
		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
			$blog_category = new BlogCategory();
			$i = 0;
			$result = array();
			foreach ($posts as $post)
			{
				$result[$i]['id_post'] = $post['id_wt_blog_post'];
				$result[$i]['viewed'] = $post['viewed'];
				$result[$i]['is_featured'] = $post['is_featured'];
				$result[$i]['meta_title'] = $post['meta_title'];
				$result[$i]['short_description'] = $post['short_description'];
				$result[$i]['meta_description'] = $post['meta_description'];
				$result[$i]['content'] = $post['content'];
				$result[$i]['meta_keyword'] = $post['meta_keyword'];
				$result[$i]['id_category'] = $post['id_category'];
				$result[$i]['link_rewrite'] = $post['link_rewrite'];
				$result[$i]['cat_name'] = $blog_category->getCatName($post['id_category']);
				$result[$i]['cat_link_rewrite'] = $blog_category->getCatLinkRewrite($post['id_category']);
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
	public static function SmartBlogSearchPostCount($keyword = null, $id_lang = null)
	{
		if ($keyword == null)
			return false;
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post_lang pl, '._DB_PREFIX_.'wt_blog_post p 
				WHERE pl.id_lang='.$id_lang.'
				AND pl.id_wt_blog_post=p.id_wt_blog_post AND p.active = 1 AND 
				(pl.meta_title LIKE \'%'.$keyword.'%\' OR
				 pl.meta_keyword LIKE \'%'.$keyword.'%\' OR
				 pl.meta_description LIKE \'%'.$keyword.'%\' OR
				 pl.content LIKE \'%'.$keyword.'%\') ORDER BY p.id_wt_blog_post DESC';
		if (!$posts = Db::getInstance()->executeS($sql))
			return false;
		return count($posts);
	}
	public static function getBlogImage()
	{
		$sql = 'SELECT id_wt_blog_post FROM '._DB_PREFIX_.'wt_blog_post';
		if (!$result = Db::getInstance()->executeS($sql))
			return false;
		return $result;
	}
	public static function GetPostSlugById($id_post, $id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post 
			WHERE pl.id_lang='.$id_lang.'
			AND p.active= 1 AND p.id_wt_blog_post = '.$id_post;
		if (!$post = Db::getInstance()->executeS($sql))
			return false;
		return $post[0]['link_rewrite'];
	}

	public static function GetPostMetaByPost($id_post, $id_lang = null)
	{
		$meta = array();
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
				'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post 
				WHERE pl.id_lang='.$id_lang.'
				AND p.active= 1 AND p.id_wt_blog_post = '.$id_post;
		if (!$post = Db::getInstance()->executeS($sql))
			return false;
		if ($post[0]['meta_title'] == '' && $post[0]['meta_title'] == null)
			$meta['meta_title'] = Configuration::get('wtblogmetatitle');
		else
			$meta['meta_title'] = $post[0]['meta_title'];
		if ($post[0]['meta_description'] == '' && $post[0]['meta_description'] == null)
			$meta['meta_description'] = Configuration::get('wtblogmetadescrip');
		else
			$meta['meta_description'] = $post[0]['meta_description'];
		if ($post[0]['meta_keyword'] == '' && $post[0]['meta_keyword'] == null)
			$meta['meta_keywords'] = Configuration::get('wtblogmetakeyword');
		else
			$meta['meta_keywords'] = $post[0]['meta_keyword'];
		return $meta;
	}

	public static function GetPostLatestHome($limit)
	{
		if ($limit == '' && $limit == null)
			$limit = 3;
		$id_lang = (int)Context::getContext()->language->id;
		$id_lang_defaut = Configuration::get('PS_LANG_DEFAULT');
		$result = array();
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
			WHERE pl.id_lang='.$id_lang.' 		
			AND p.active= 1 ORDER BY p.id_wt_blog_post DESC 
			LIMIT '.$limit;
		$posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if (empty($posts))
		{
			$sql2 = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_post p INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_lang pl ON p.id_wt_blog_post=pl.id_wt_blog_post INNER JOIN 
			'._DB_PREFIX_.'wt_blog_post_shop ps ON pl.id_wt_blog_post = ps.id_wt_blog_post  AND ps.id_shop = '.(int)Context::getContext()->shop->id.'
			WHERE pl.id_lang='.$id_lang_defaut.' 		
			AND p.active= 1 ORDER BY p.id_wt_blog_post DESC 
			LIMIT '.$limit;
			$posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql2);
		}
		$i = 0;
		foreach ($posts as $post)
		{
			$result[$i]['id'] = $post['id_wt_blog_post'];
			$result[$i]['title'] = $post['meta_title'];
			$result[$i]['meta_description'] = strip_tags($post['meta_description']);
			$result[$i]['short_description'] = strip_tags($post['short_description']);
			$result[$i]['content'] = strip_tags($post['content']);
			$result[$i]['category'] = $post['id_category'];
			$result[$i]['date_added'] = $post['created'];
			$result[$i]['viewed'] = $post['viewed'];
			$result[$i]['is_featured'] = $post['is_featured'];
			$result[$i]['link_rewrite'] = $post['link_rewrite'];
			if (file_exists(_PS_MODULE_DIR_.'wtblog/views/img/'.$post['id_wt_blog_post'].'.jpg'))
			{
				$image = $post['id_wt_blog_post'];
				$result[$i]['post_img'] = $image;
			}
			else
				$result[$i]['post_img'] = 'no';
			$i++;
		}
		return $result;
	}
}