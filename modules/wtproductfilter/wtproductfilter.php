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

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'wtproductfilter/classes/WtProductFilterClass.php');
include_once(_PS_MODULE_DIR_.'wtproductfilter/sql/SampleDataProdFilter.php');

class WtProductFilter extends Module
{
	protected $config_form = false;
	private $html = '';
	private $hook_into = array('displayHome', 'displayTopColumn', 'displayLeftColumn','displayRightColumn','displayTopHome', 'displayBottomHome');
	private $type_display = array('accordion', 'tab', 'column');
	
	public function __construct()
	{
		$this->name = 'wtproductfilter';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'waterthemes';
		$this->need_instance = 1;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('WT Product Filter');
		$this->description = $this->l('Get product Filter');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}
	
	public function install()
	{
		$res = true;
		$res &= parent::install() && $this->registerHook('header') && $this->registerHook('displayHome') && $this->registerHook('displayTopColumn') && $this->registerHook('displayLeftColumn') && $this->registerHook('displayRightColumn') && $this->registerHook('displayTopHome') && $this->registerHook('displayTopHome') && $this->registerHook('displayBottomHome') && $this->registerHook('displayHome') && $this->registerHook('addproduct') && $this->registerHook('updateproduct') && $this->registerHook('deleteproduct') && $this->registerHook('categoryUpdate') && $this->registerHook('actionShopDataDuplication') && $this->registerHook('actionObjectLanguageAddAfter');
		include(dirname(__FILE__).'/sql/install.php');
		$sampleData = new SampleDataProdFilter();
		$res &= $sampleData->initData();
		return $res;
	}
	public function uninstall()
	{
		include(dirname(__FILE__).'/sql/uninstall.php');
		return parent::uninstall();
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitCatProd') || Tools::isSubmit('delete_id_group_cat') || Tools::isSubmit('changeStatus'))
		{
			$this->_postProcess();
			$this->html .= $this->renderAddForm();
		}
		elseif (Tools::isSubmit('addCat') || (Tools::isSubmit('id_wtproductfilter') && $this->catExists(Tools::getValue('id_wtproductfilter'))))
			$this->html .= $this->renderAddForm();
		else
		{
			$this->_postProcess();
			$this->context->smarty->assign('module_dir', $this->_path);
			$this->html .= $this->renderList();
		}
		return $this->html;
	}

	public function renderList()
	{
		$info_category = $this->getCatInfo();
		foreach ($info_category as $key => $info_cat)
			$info_category[$key]['status'] = $this->displayStatus($info_cat['id_wtproductfilter'], $info_cat['active']);

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'info_category' => $info_category
			)
		);
		return $this->display(__FILE__, 'views/templates/admin/list.tpl');
	}
	
	public function getCatInfo($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = (int)$this->context->shop->id;
		$id_lang = (int)$this->context->language->id;
		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT pc.*
			FROM '._DB_PREFIX_.'wtproductfilter_shop pc
			WHERE pc.id_shop = '.$id_shop.($active ? ' AND pc.`active` = 1' : ' ')
		);
	}
	
	public function displayStatus($id_wtproductfilter, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_wtproductfilter='.(int)$id_wtproductfilter.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}
	
	protected function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitCatProd'))
		{
			$this->clearCacheProdFilter();
			if (Tools::getValue('id_wtproductfilter'))
			{
				$cat_group = new WtProductFilterClass((int)Tools::getValue('id_wtproductfilter'));
				if (!Validate::isLoadedObject($cat_group))
				{
					$this->html .= $this->displayError($this->l('Invalid id_wtproductfilter'));
					return false;
				}
			}
			else
				$cat_group = new WtProductFilterClass();
			$cat_group->active = (int)Tools::getValue('active_cat');
			$cat_group->id_hook = Tools::getValue('id_hook');
			$cat_group->type_display = Tools::getValue('type_display');
			$cat_group->num_show = Tools::getValue('num_show');
			$cat_group->use_slider = Tools::getValue('use_slider');
			$cat_group->show_sub  = Tools::getValue('show_sub ');
			$cat_group->shownew = Tools::getValue('shownew');
			$cat_group->showfeature = Tools::getValue('showfeature');
			$cat_group->showsale = Tools::getValue('showsale');
			$cat_group->showspecail = Tools::getValue('showspecail');
		
			if (!$errors)
			{
				if (!Tools::getValue('id_wtproductfilter'))
				{
					if (!$cat_group->add())
						$errors[] = $this->displayError($this->l('The cat_group could not be added.'));
				}
				else
				{
					if (!$cat_group->update())
						$errors[] = $this->displayError($this->l('The cat_group could not be updated.'));
				}
			}
			return $errors;
		}
		elseif (Tools::isSubmit('changeStatus') && Tools::getValue('id_wtproductfilter'))
		{
			$this->clearCacheProdFilter();
			$group_cat = new WtProductFilterClass(Tools::getValue('id_wtproductfilter'));
			if ($group_cat->active == 0)
				$group_cat->active = 1;
			else
				$group_cat->active = 0;
			$res = $group_cat->update();
			$this->html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('The configuration could not be updated.')));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('delete_id_group_cat'))
		{
			$this->clearCacheProdFilter();
			$prod_filter_item = new WtProductFilterClass((int)Tools::getValue('delete_id_group_cat'));
			$res = $prod_filter_item->delete();
			if (!$res)
				$this->html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
	}
	public function getHookList()
	{
		$hooks = array();
		
		foreach ($this->hook_into as $key => $hook)
		{
			$hooks[$key]['key'] = $hook;
			$hooks[$key]['name'] = $hook;
		}
		return $hooks;
	}
	
	public function getTypeList()
	{
		$hooks = array();
		
		foreach ($this->type_display as $key => $type)
		{
			$hooks[$key]['key'] = $type;
			$hooks[$key]['name'] = $type;
		}
		return $hooks;
	}
	
	public function renderAddForm()
	{
		$selected_categories = array();
		$hook_into = $this->getHookList();
		$type = $this->getTypeList();
		
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Block Product'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Show New Product'),
						'name' => 'shownew',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'shownew_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'shownew_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Feature Product'),
						'name' => 'showfeature',
						'is_bool' => true,
						'values'  => array(
							array(
								'id' => 'showfeature_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'showfeature_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)
					),
					array(
						'type'    => 'switch',
						'label'   => $this->l('Besteller'),
						'name'    => 'showsale',
						'is_bool' => true,
						'values'  => array(
							array(
								'id' => 'showsale_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'showsale_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Specail Product'),
						'name' => 'showspecail',
						'is_bool' => true,
						'values'  => array(
							array(
								'id' => 'showspecail_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'showspecail_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Hook'),
						'name' => 'id_hook',
						'options' => array(
							'query' => $hook_into, 
							'id' => 'key',
							'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Type display'),
						'desc' => $this->l(''),
						'name' => 'type_display',
						'options' => array(
							'query' => $type, 
							'id' => 'key',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('number product'),
						'desc' => $this->l(''),
						'name' => 'num_show'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Use Slider'),
						'name' => 'use_slider',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'useslider_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'useslider_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'active_cat',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
				'buttons' => array(
					array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'title' => $this->l('Back to list'),
					'icon' => 'process-icon-back'
					)
				)
			),
		);
		if (Tools::isSubmit('id_wtproductfilter') && $this->catExists((int)Tools::getValue('id_wtproductfilter')))
		{
			$slide = new WtProductFilterClass((int)Tools::getValue('id_wtproductfilter'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_wtproductfilter');
		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitCatProd';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		$helper->override_folder = '/';

		return $helper->generateForm(array($fields_form));
	}
	
	public function getAddFieldsValues()
	{
		$fields = array();
		$languages = Language::getLanguages(false);
		if (Tools::isSubmit('id_wtproductfilter') && $this->catExists((int)Tools::getValue('id_wtproductfilter')))
		{
			$group_cat = new WtProductFilterClass((int)Tools::getValue('id_wtproductfilter'));
			
			$fields['shownew'] = Tools::getValue('shownew', $group_cat->shownew);
			$fields['showfeature'] = Tools::getValue('showfeature', $group_cat->showfeature);
			$fields['showsale'] = Tools::getValue('showsale', $group_cat->showsale);
			$fields['showspecail'] = Tools::getValue('showspecail', $group_cat->showspecail);
			$fields['id_wtproductfilter'] = (int)Tools::getValue('id_wtproductfilter', $group_cat->id);
			$fields['active_cat'] = Tools::getValue('active_cat', $group_cat->active);
			$fields['id_hook'] = Tools::getValue('id_hook', $group_cat->id_hook);
			$fields['type_display'] = Tools::getValue('type_display', $group_cat->type_display);
			$fields['num_show'] = Tools::getValue('num_show', $group_cat->num_show);
			$fields['use_slider'] = Tools::getValue('use_slider', $group_cat->use_slider);
		}
		else
		{
			$fields['shownew'] = Tools::getValue('shownew', 1);
			$fields['showfeature'] = Tools::getValue('showfeature', 1);
			$fields['showsale'] = Tools::getValue('showsale', 1);
			$fields['showspecail'] = Tools::getValue('showspecail', 1);
			$fields['active_cat'] = Tools::getValue('active_cat', 1);
			$fields['id_hook'] = Tools::getValue('id_hook', 1);
			$fields['type_display'] = Tools::getValue('type_display', 1);
			$fields['num_show'] = Tools::getValue('num_show', 8);
			$fields['use_slider'] = Tools::getValue('use_slider', 1);
		}
		return $fields;
	}
	
	public function catExists($id)
	{
		$req = 'SELECT wt.`id_wtproductfilter` as id_wtproductfilter
				FROM `'._DB_PREFIX_.'wtproductfilter` wt
				WHERE wt.`id_wtproductfilter` = '.(int)$id;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}
	public function hookHeader()
	{
		// $this->context->controller->addJS($this->_path.'/views/js/front.js');
		if ($this->context->controller->php_self == 'index')
			$this->context->controller->addCSS($this->_path.'/views/css/front.css');
	}
	public function prevHook($hook_name, $params)
	{
		$id_lang = (int)$this->context->language->id;
		$block_prod = new WtProductFilterClass();
		$block_prods = $block_prod->getBlockByHook($hook_name);
		$new_block_prod = array();
		$nb = 10;
		foreach ($block_prods as $block_prod_item)
		{
			$product_group = array();
			if ($block_prod_item['shownew'] == 1)
			{
				$prod_group = array();
				$prod_group['title'] = $this->l('New Product');
				$prod_group['class'] = $this->l('wt-new-prod');
				$nb = $block_prod_item['num_show'];
				$prod_group['product_list'] = Product::getNewProducts($id_lang, 0, $nb);
				$product_group[] = $prod_group;
			}
			if ($block_prod_item['showfeature'] == 1)
			{
				$prod_group = array();
				$prod_group['title'] = $this->l('Feature Product');
				$prod_group['class'] = $this->l('wt-feature-prod');
				$category = new Category(Context::getContext()->shop->getCategory(), $id_lang);
				$nb = $block_prod_item['num_show'];
				$prod_group['product_list'] = $category->getProducts($id_lang, 1, $nb);
				$product_group[] = $prod_group;
			}
			if ($block_prod_item['showsale'] == 1)
			{
				$prod_group = array();
				$prod_group['title'] = $this->l('Best Seller');
				$prod_group['class'] = $this->l('wt-bestseller-prod');
				$nb = $block_prod_item['num_show'];
				$prod_group['product_list'] = $this->getBestSellers($params, $nb);
				$product_group[] = $prod_group;
			}
			if ($block_prod_item['showspecail'] == 1)
			{
				$prod_group = array();
				$prod_group['title'] = $this->l('Specail Product');
				$prod_group['class'] = $this->l('wt-specail-prod');
				$nb = $block_prod_item['num_show'];
				$prod_group['product_list'] = Product::getPricesDrop($id_lang, 0, $nb);
				$product_group[] = $prod_group;
			}
			
			$block_prod_item['product_group'] = $product_group;
			$new_block_prod[] = $block_prod_item;	
		}
		return $new_block_prod;
	}
	
	protected function getBestSellers($params, $nb)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;

		if (!($result = ProductSale::getBestSalesLight((int)$params['cookie']->id_lang, 0, $nb)))
			return false;

		$currency = new Currency($params['cookie']->id_currency);
		$usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
		foreach ($result as &$row)
			$row['price'] = Tools::displayPrice(Product::getPriceStatic((int)$row['id_product'], $usetax), $currency);

		return $result;
	}
	
	public function hookDisplayHome($params)
	{
		$id_lang = $this->context->language->id;
		$group_prod_fliter = array();
		if (!$this->isCached('wtproductfilter_home.tpl', $this->getCacheId('wtproductfilter_home')))
		{
			$group_prod_fliter = $this->prevHook('displayHome', $params);
			if (!isset($group_prod_fliter) || count($group_prod_fliter) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_prod_fliter' => $group_prod_fliter,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		}
		return $this->display(__FILE__, 'wtproductfilter_home.tpl', $this->getCacheId('wtproductfilter_home'));
	}
	
	public function hookdisplayTopColumn($params)
	{
		$id_lang = $this->context->language->id;
		$group_prod_fliter = array();
		if (!$this->isCached('wtproductfilter_topcolumn.tpl', $this->getCacheId('wtproductfilter_topcolumn')))
		{
			$group_prod_fliter = $this->prevHook('displayTopColumn', $params);
			if (!isset($group_prod_fliter) || count($group_prod_fliter) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_prod_fliter' => $group_prod_fliter,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		}
		return $this->display(__FILE__, 'wtproductfilter_topcolumn.tpl', $this->getCacheId('wtproductfilter_topcolumn'));
	}
	
	public function hookdisplayLeftColumn($params)
	{
		$id_lang = $this->context->language->id;
		$group_prod_fliter = array();
		if (!$this->isCached('wtproductfilter_leftcolumn.tpl', $this->getCacheId('wtproductfilter_leftcolumn')))
		{
			$group_prod_fliter = $this->prevHook('displayLeftColumn', $params);
			if (!isset($group_prod_fliter) || count($group_prod_fliter) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_prod_fliter' => $group_prod_fliter,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		}
		return $this->display(__FILE__, 'wtproductfilter_leftcolumn.tpl', $this->getCacheId('wtproductfilter_leftcolumn'));
	}
	
	public function hookdisplayRightColumn($params)
	{
		$id_lang = $this->context->language->id;
		$group_prod_fliter = array();
		if (!$this->isCached('wtproductfilter_rightcolumn.tpl', $this->getCacheId('wtproductfilter_rightcolumn')))
		{
			$group_prod_fliter = $this->prevHook('displayRightColumn', $params);
			if (!isset($group_prod_fliter) || count($group_prod_fliter) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_prod_fliter' => $group_prod_fliter,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));	
		}
		return $this->display(__FILE__, 'wtproductfilter_rightcolumn.tpl', $this->getCacheId('wtproductfilter_rightcolumn'));
	}
	
	public function hookdisplayTopHome($params)
	{
		$id_lang = $this->context->language->id;
		$group_prod_fliter = array();
		if (!$this->isCached('wtproductfilter_tophome.tpl', $this->getCacheId('wtproductfilter_tophome')))
		{
			$group_prod_fliter = $this->prevHook('displayTopHome', $params);
			if (!isset($group_prod_fliter) || count($group_prod_fliter) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_prod_fliter' => $group_prod_fliter,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		}
		return $this->display(__FILE__, 'wtproductfilter_tophome.tpl', $this->getCacheId('wtproductfilter_tophome'));
	}
	
	public function hookdisplayBottomHome($params)
	{
		$id_lang = $this->context->language->id;
		$group_prod_fliter = array();
		if (!$this->isCached('wtproductfilter_bottomhome.tpl', $this->getCacheId('wtproductfilter_bottomhome')))
		{
			$group_prod_fliter = $this->prevHook('displayBottomHome', $params);
			if (!isset($group_prod_fliter) || count($group_prod_fliter) <= 0)
				return false;
			$this->context->smarty->assign(array(
				'group_prod_fliter' => $group_prod_fliter,
				'banner_path' => $this->_path.'views/img/banners/',
				'icon_path' => $this->_path.'views/img/icons/',
			));
		}
		return $this->display(__FILE__, 'wtproductfilter_bottomhome.tpl', $this->getCacheId('wtproductfilter_bottomhome'));
	}
	
	public function hookAddProduct()
	{
		$this->clearCacheProdFilter();
	}
	public function hookUpdateProduct()
	{
		$this->clearCacheProdFilter();
	}
	public function hookDeleteProduct()
	{
		$this->clearCacheProdFilter();
	}
	public function hookCategoryUpdate()
	{
		$this->clearCacheProdFilter();
	}
	public function clearCacheProdFilter()
	{
		$this->_clearCache('wtproductfilter_bottomhome.tpl');
		$this->_clearCache('wtproductfilter_tophome.tpl');
		$this->_clearCache('wtproductfilter_rightcolumn.tpl');
		$this->_clearCache('wtproductfilter_leftcolumn.tpl');
		$this->_clearCache('wtproductfilter_topcolumn.tpl');
		$this->_clearCache('wtproductfilter_home.tpl');
	}
	
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'wtproductfilter_shop (`id_wtproductfilter`, `shownew`, `showfeature`, `showsale`, `showspecail`, `id_shop`, `id_hook`, `type_display`, `num_show`, `use_slider`, `active`)
			SELECT `id_wtproductfilter`, `shownew`, `showfeature`, `showsale`, `showspecail`, '.(int)$params['new_id_shop'].', `id_hook`, `type_display`, `num_show`, `use_slider`, `active`
			FROM '._DB_PREFIX_.'wtproductfilter_shop
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
	}
}