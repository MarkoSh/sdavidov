<?php
/**
* 2007-2014 PrestaShop
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
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class WtBannerClass extends ObjectModel
{
	public $id_wtbanner;
	public $file_name;
	public $text;
	public $link;
	public $title;
	public $active;
	
	public $temp_url = '{wtbanner_url}';
	public static $definition = array(
		'table' => 'wtbanner',
		'primary' => 'id_wtbanner',
		'multilang' => true,
		'fields' => array(
			'file_name' =>				array('type' => self::TYPE_STRING, 'validate' => 'isFileName'),
			'active'  => 		array('type' => self::TYPE_BOOL,'shop' => true),
			'title' =>				array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isString'),
			'link'=>array('type' => self::TYPE_STRING,'validate' => 'isString','lang' => true),
			'text' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'required' => false),
			
		)
	);
	
	public	function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id, $id_lang, $id_shop);
		if ($this->id)
		{
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
				foreach ($this->fieldsValidateLang as $field => $validation)
				{	
					if (isset($this->{$field}[(int)($language['id_lang'])]))
					{
						$temp = str_replace($this->temp_url, _PS_BASE_URL_.__PS_BASE_URI__, $this->{$field}[(int)($language['id_lang'])]);
						$this->{$field}[(int)($language['id_lang'])] = $temp;
					}
				}
			$this->active = $this->getFieldShop('active');
		}
		Shop::addTableAssociation('wtbanner', array('type' => 'shop'));
		Shop::addTableAssociation('wtbanner_lang', array('type' => 'fk_shop'));
	}
	
	public function getFieldShop($field)
	{
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$sql = 'SELECT bs.'.$field.' FROM '._DB_PREFIX_.'wtbanner b
		LEFT JOIN '._DB_PREFIX_.'wtbanner_shop bs ON (b.id_wtbanner = bs.id_wtbanner)
		WHERE b.id_wtbanner = '.$this->id.' AND bs.id_shop = '.$id_shop.'';
		return Db::getInstance()->getValue($sql);
	}
	public function bannerExistShop()
	{
		$sql = 'SELECT b.id_wtbanner FROM '._DB_PREFIX_.'wtbanner b
		WHERE b.id_wtbanner = '.$this->id_wtbanner;
		return Db::getInstance()->getValue($sql);
	}
	public function copyFromPost()
	{
		$this->filename = Tools::getValue('filename');
		$this->active = Tools::getValue('active');
		if (count($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
				foreach ($this->fieldsValidateLang as $field => $validation)
					if (Tools::getIsset($field.'_'.(int)($language['id_lang'])))
						$this->{$field}[(int)($language['id_lang'])] = Tools::getValue($field.'_'.(int)($language['id_lang']));
		}
	}
}
