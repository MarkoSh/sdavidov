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

class BlogCategory extends ObjectModel
{
	public $id_wt_blog_category;
	public $id_parent;
	public $position;
	public $desc_limit;
	public $active = 1;
	public $created;
	public $modified;
	public $meta_title;
	public $meta_keyword;
	public $meta_description;
	public $description;
	public $link_rewrite;
		
	public static $definition = array(
		'table' => 'wt_blog_category',
		'primary' => 'id_wt_blog_category',
		'multilang'=>true,
		'fields' => array(
			'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'position' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'desc_limit' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'created' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
			'modified' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
			'meta_title' => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isString','required' => true),
			'meta_keyword' => array('type' => self::TYPE_STRING, 'validate' => 'isString','lang'=>true),
			'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
			'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
			'link_rewrite' => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isString','required' => true)
		),
	);
	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		Shop::addTableAssociation('wt_blog_category', array('type' => 'shop'));
		parent::__construct($id, $id_lang, $id_shop);
	}

	public static function getRootCategory($id_lang = null)
	{
		if ($id_lang == null)
		$id_lang = (int)Context::getContext()->language->id;
		$root_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'wt_blog_category` sbc INNER JOIN `'._DB_PREFIX_.'wt_blog_category_lang` sbcl ON(sbc.`id_wt_blog_category` = sbcl.`id_wt_blog_category` AND sbcl.`id_lang` = '.(int)($id_lang).')
		INNER JOIN `'._DB_PREFIX_.'wt_blog_category_lang` sbs ON sbs.id_wt_blog_category = sbc.id_wt_blog_category and sbs.id_shop = '.(int)Context::getContext()->shop->id.'   WHERE sbc.`active`= 1 AND sbc.id_parent = 0');
		return $root_category;
	}
		
	public static function getNameCategory($id)
	{
		$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'wt_blog_category_lang pl, '._DB_PREFIX_.'wt_blog_category p 
			WHERE pl.id_wt_blog_category=p.id_wt_blog_category AND p.id_wt_blog_category='.$id.' AND pl.id_lang = '.$id_lang;
		if (!$result = Db::getInstance()->executeS($sql))
			return false;
		return $result;
	}
	
	public static function getCatName($id)
	{
		$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT pl.meta_title FROM '._DB_PREFIX_.'wt_blog_category_lang pl, '._DB_PREFIX_.'wt_blog_category p 
			WHERE pl.id_wt_blog_category=p.id_wt_blog_category AND p.id_wt_blog_category='.$id.' AND pl.id_lang = '.$id_lang;
		if (!$result = Db::getInstance()->executeS($sql))
			return false;
		return $result[0]['meta_title'];
	}

	public static function getCatLinkRewrite($id)
	{
		$id_lang = (int)Context::getContext()->language->id;
		$sql = 'SELECT pl.link_rewrite FROM '._DB_PREFIX_.'wt_blog_category_lang pl, '._DB_PREFIX_.'wt_blog_category p 
			WHERE pl.id_wt_blog_category=p.id_wt_blog_category AND p.id_wt_blog_category='.$id.' AND pl.id_lang = '.$id_lang;
		if (!$result = Db::getInstance()->executeS($sql))
			return false;
		return $result[0]['link_rewrite'];
	}
	public static function getCatImage()
	{
		$sql = 'SELECT id_wt_blog_category FROM '._DB_PREFIX_.'wt_blog_category';
		if (!$result = Db::getInstance()->executeS($sql))
			return false;
		return $result;
	}
	public static function getCategory($active = 1, $id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = (int)Context::getContext()->language->id;
		
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'wt_blog_category` sbc INNER JOIN `'._DB_PREFIX_.'wt_blog_category_lang` sbcl ON(sbc.`id_wt_blog_category` = sbcl.`id_wt_blog_category` AND sbcl.`id_lang` = '.(int)$id_lang.')
		INNER JOIN `'._DB_PREFIX_.'wt_blog_category_shop` sbs ON sbs.id_wt_blog_category = sbc.id_wt_blog_category and sbs.id_shop = '.(int)Context::getContext()->shop->id.' WHERE sbc.`active`= 1';
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		return $result;
	}
	public static function getCategoryNameByPost($id_post)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.id_category FROM `'._DB_PREFIX_.'wt_blog_post` p where p.id_wt_blog_post =  '.$id_post);
		return $result[0]['id_category'];
	}
	
	public static function getPostByCategory($id_wt_blog_category)
	{
		$sql = 'select count(id_wt_blog_post) as count from `'._DB_PREFIX_.'wt_blog_post` where id_category = '.$id_wt_blog_category;
		if (!$result = Db::getInstance()->executeS($sql))
			return false;
		return $result[0]['count'];	
	}
	public static function GetMetaByCategory($id_category, $id_lang = null)
	{
		$meta = array();
		if ($id_lang == null) $id_lang = (int)Context::getContext()->language->id;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT * FROM `'._DB_PREFIX_.'wt_blog_category` sbc INNER JOIN `'._DB_PREFIX_.'wt_blog_category_lang` sbcl ON(sbc.`id_wt_blog_category` = sbcl.`id_wt_blog_category` AND sbcl.`id_lang` = '.(int)$id_lang.')
		INNER JOIN `'._DB_PREFIX_.'wt_blog_category_shop` sbs ON sbs.id_wt_blog_category = sbc.id_wt_blog_category AND sbs.id_shop = '.(int)Context::getContext()->shop->id.' WHERE sbc.`active`= 1 AND sbc.id_wt_blog_category = '.$id_category);
				
		if ($result[0]['meta_title'] == '' && $result[0]['meta_title'] == null)
			$meta['meta_title'] = Configuration::get('smartblogmetatitle');
		else
			$meta['meta_title'] = $result[0]['meta_title'];
		
		if ($result[0]['meta_description'] == '' && $result[0]['meta_description'] == null)
			$meta['meta_description'] = Configuration::get('smartblogmetadescrip');
		else
			$meta['meta_description'] = $result[0]['meta_description'];
		
		if ($result[0]['meta_keyword'] == '' && $result[0]['meta_keyword'] == null)
			$meta['meta_keywords'] = Configuration::get('smartblogmetakeyword');
		else
			$meta['meta_keywords'] = $result[0]['meta_keyword'];
		return $meta;
	}
}