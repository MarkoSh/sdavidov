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

class WtSearchPopular extends Module
{
	public function __construct()
	{
		$this->name = 'wtsearchpopular';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'waterthemes';
		parent::__construct();
		$this->displayName = $this->l('WT Popular search word');
		$this->description = $this->l('Adds a block popular search.');
	}
	public function install()
	{
		return (parent::install() && $this->registerHook('displayFooter') && $this->registerHook('actionSearch'));
	}
	public function uninstall()
	{
		if (parent::uninstall() == false)
			return false;
		$this->_clearCache('wtsearchpopular.tpl');
		return true;
	}
	public static function getPopularWord($id_lang, $id_shop)
	{
		$search_list = Db::getInstance()->executeS('
		SELECT COUNT(sw.`word`) AS total,sw.`word` FROM '._DB_PREFIX_.'search_word sw
		LEFT JOIN '._DB_PREFIX_.'search_index si 
		ON (sw.id_word = si.id_word AND sw.id_lang = '.(int)$id_lang.' 
		AND sw.id_shop = '.$id_shop.') 
		GROUP BY sw.`word` ORDER BY total DESC LIMIT 30 ');
		return $search_list;
	}
	public function hookDisplayFooter()
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$smarty_cache_id = $this->getCacheId('wtsearchpopular');
		if (!$this->isCached('wtsearchpopular.tpl', $smarty_cache_id))
		{
			$context = Context::GetContext();
			$id_lang = $context->language->id;
			$id_shop = $context->shop->id;
			$search_list = $this->getPopularWord($id_lang, $id_shop);
			$this->context->smarty->assign(array('searchList' => $search_list));
		}
		return $this->display(__FILE__, 'wtsearchpopular.tpl', $smarty_cache_id);
	}
	public function hookActionSearch()
	{
		$this->_clearCache('wtsearchpopular.tpl');
	}
}