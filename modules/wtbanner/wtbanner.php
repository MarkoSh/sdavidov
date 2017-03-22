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

if (!defined('_CAN_LOAD_FILES_'))
	exit;

include_once _PS_MODULE_DIR_.'wtbanner/classes/WtBannerClass.php';
include_once _PS_MODULE_DIR_.'wtbanner/sql/SampleData.php';

class WtBanner extends Module
{
	private $temp_url = '{wtbanner_url}';
	private $html;
	private $settings_default;
	private $cs_name_config;
	private $config;
	public function __construct()
	{
		$this->name = 'wtbanner';
		$this->tab = 'front_office_features';
		$this->version = '2.0.0';
		$this->author = 'waterthemes';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('WT Banners Block');
		$this->description = $this->l('Add the banners with the information specified by the user.');
		$this->settings_default = array (
			'used_slider' => 0,
			'number_banner_aline' => 2
		);
		$this->cs_name_config = 'WT_CONFIG_BANNER';
		$this->getInitSettings();
	}
	public function getInitSettings()
	{
		$this->config = (array)Tools::jsonDecode(Configuration::get($this->cs_name_config));
		$this->config = (object)array_merge((array)$this->settings_default, $this->config);
	}
	public function install()
	{
		if (!parent::install() || !$this->registerHook('actionShopDataDuplication') || !$this->registerHook('displayHeader') || !$this->registerHook('displayHome') || !$this->registerHook('actionObjectLanguageAddAfter'))
			return false;
		if (!Configuration::hasKey($this->cs_name_config))
			Configuration::updateValue($this->cs_name_config, '');
		include(dirname(__FILE__).'/sql/install.php');
		$sample_data = new SampleDataBanner();
		if (!$sample_data->initData())
			return false;
		return true;	
	}
	public function uninstall()
	{
		include(dirname(__FILE__).'/sql/uninstall.php');
		return parent::uninstall();
	}
	public function getItemsPerLineList()
	{
		$products_per_line = array();
		$i = 0;
		for ($i = 1; $i <= 8; $i++)
			$products_per_line[$i]['number'] = $i;
		return $products_per_line;
	}
	public function checkValidate()
	{
		$configs = Tools::getValue('config');
		$errors = array();
		foreach ($configs as $key_option => $value_option)
		{
			$pos = strpos($key_option, 'number_');
			if ($pos !== false)
				if (isset($value_option) && (!$value_option || $value_option <= 0 || !Validate::isInt($value_option)))
					$errors[] = $this->l('An invalid '.$key_option.' has been specified.');
		}
		return $errors;
	}
	public function postProcess()
	{		
		if (Tools::isSubmit('saveBanner'))
		{
			$languageDefault = Configuration::get('PS_LANG_DEFAULT');
			if ((!isset($_FILES['image']['tmp_name']) || $_FILES['image']['tmp_name'] == '') && !Tools::getValue('id_wtbanner'))
				$this->html .= $this->displayError($this->l('Banner empty !'));
			else if (Tools::getValue('title_'.$languageDefault.'') == '')
				$this->html .= $this->displayError($this->l('Title for language default empty !'));
			else 
			{
				$reinsurance = new WtBannerClass(Tools::getValue('id_wtbanner'));
				$reinsurance->copyFromPost();
				
				if ($reinsurance->validateFields(false) && $reinsurance->validateFieldsLang(false))
				{
					$reinsurance->save();
					if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']))
					{
						$id_shop = $this->context->shop->id;
						if ($error = ImageManager::validateUpload($_FILES['image']))
							return false;
						elseif (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmpName))
							return false;
						elseif (!ImageManager::resize($tmpName, dirname(__FILE__).'/views/img/reinsurance-'.(int)$reinsurance->id.'-'.$id_shop.'.jpg'))
							return false;
						unlink($tmpName);
						$reinsurance->file_name = 'reinsurance-'.(int)$reinsurance->id.'-'.$id_shop.'.jpg';
						$reinsurance->save();
					}
					Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
				}
				else
					$this->html .= '<div class="conf error">'.$this->l('An error occurred while attempting to save.').'</div>';
			}
		}
		elseif (Tools::isSubmit('changeStatus') && Tools::getValue('id_wtbanner'))
		{
			$banner = new WtBannerClass(Tools::getValue('id_wtbanner'));
			if ($banner->active == 0)
				$banner->active = 1;
			else
				$banner->active = 0;
			$res = $banner->update();
			$this->html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('The configuration could not be updated.')));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else if (Tools::isSubmit('saveConfig'))
		{
			$errors = $this->checkValidate();
			if (isset($errors) && count($errors))
				$this->html .= $this->displayError(implode('<br />', $errors));
			else
			{
				$config = Tools::getValue('config');
				if ($config)
				{
					$config = Tools::jsonEncode($config);
					Configuration::updateValue($this->cs_name_config, $config);
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&successConfirmation');
				}
			}
		}
		else if (Tools::isSubmit('deleteBanner'))
		{
			$banner = new WtBannerClass(Tools::getValue('id_wtbanner'));
			$banner->delete();
			if (file_exists(dirname(__FILE__).'/views/img/'.$banner->file_name) && !$banner->bannerExistShop())
				unlink(dirname(__FILE__).'/views/img/'.$banner->file_name);
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else if (Tools::isSubmit('successConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Your settings have been updated.'));
	}
	public function getContent()
	{
		$this->postProcess();
		if (Tools::isSubmit('addBanner') || Tools::isSubmit('editBanner') || Tools::isSubmit('saveBanner'))
			$this->initForm();
		else
		{
			$this->html .= $this->renderList();
			$this->displaySettings();
		}
		return $this->html;
	}
	
	private function getBanners($active = null)
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
		if (!$result = Db::getInstance()->ExecuteS(
			'SELECT bs.*, bl.`title`,bl.`link`
			FROM `'._DB_PREFIX_.'wtbanner` b
			LEFT JOIN `'._DB_PREFIX_.'wtbanner_shop` bs ON (bs.`id_wtbanner` = b.`id_wtbanner` )
			LEFT JOIN `'._DB_PREFIX_.'wtbanner_lang` bl ON (b.`id_wtbanner` = bl.`id_wtbanner` '.( $id_shop ? 'AND bl.`id_shop` = '.$id_shop : ' ' ).') 
			WHERE bl.id_lang = '.(int)$id_lang.
			($active ? ' AND bs.`active` = 1' : ' ').
			( $id_shop ? 'AND bs.`id_shop` = '.$id_shop : ' ' ).''))
			return false;
		return $result;
	}
	public function displaySettings()
	{
		$lang_default = Configuration::get('PS_LANG_DEFAULT');
		$fields_form = array();
		include(dirname(__FILE__).'/classes/settings.php');
		$this->fields_form[0]['form'] = $fields_form; /* load form config */
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->title = $this->displayName;
		$helper->submit_action = 'saveConfig';
		if (Tools::getIsset('config'))
			$this->config = (object)array_merge(Tools::getValue('config'), (array)$this->config);
		foreach ($this->fields_form[0]['form']['input'] as $field)
		{
			$option = str_replace('config[', '', $field['name']);
			$option = str_replace(']', '', $option);
			$helper->fields_value[''.$field['name'].''] = (isset($this->config->$option) ? $this->config->$option : '');
		}
		$this->html .= $helper->generateForm($this->fields_form);
	}
	private function renderList()
	{
		if ($this->getBanners(false) && count($this->getBanners(false)) > 0)
		{
			$banners = $this->getBanners(false);
			foreach ($banners as $key => $banner)
				$banners[$key]['status'] = $this->displayStatus($banner['id_wtbanner'], $banner['active']);
		}
		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'banners' => $banners
			)
		);
		return $this->display(__FILE__, 'views/templates/admin/list.tpl');
	}
	
	public function displayStatus($id_wtbanner, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_wtbanner='.(int)$id_wtbanner.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}
	
	public function initForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$id_wtbanner = Tools::getValue('id_wtbanner');
		if ($id_wtbanner)
			$wtbanner = new wtbannerClass((int)$id_wtbanner);
		else
			$wtbanner = new wtbannerClass();
		if ($wtbanner->file_name != '')
			$banner = __PS_BASE_URI__.'modules/'.$this->name.'/views/img/'.$wtbanner->file_name;
		else
			$banner = '';
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('New Banner.'),
			),
			'input' => array(
				array(
					'type' => 'file',
					'label' => $this->l('Image:'),
					'name' => 'image',
					'value' => true,
					'banner' => $banner
				),
				array(
					'type' => 'text',
					'label' => $this->l('Title:'),
					'name' => 'title',
					'lang' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Link:'),
					'name' => 'link',
					'lang' => true
					
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Text:'),
					'lang' => true,
					'name' => 'text',
					'autoload_rte' => true,
					'cols' => 40,
					'rows' => 10
				),
				array(
						'type' => 'switch',
						'label' => $this->l('Displayed'),
						'name' => 'active',
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
						),
				),
			),
			'submit' => array(
				'title' => $this->l('Save')
			)
		);
		
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = 'wtbanner';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->show_cancel_button = true;
		$helper->back_url = AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->title = $this->displayName;
		$helper->submit_action = 'saveBanner';
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' =>
			array(
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		foreach (Language::getLanguages(false) as $lang)
		{
			$helper->fields_value['title'][(int)$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $wtbanner->title[(int)$lang['id_lang']]);
			$helper->fields_value['link'][(int)$lang['id_lang']] = Tools::getValue('link_'.(int)$lang['id_lang'], $wtbanner->link[(int)$lang['id_lang']]);
			$helper->fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], $wtbanner->text[(int)$lang['id_lang']]);
		}
		
		if (Tools::getValue('active', $wtbanner->active) != '')
			$active = Tools::getValue('active', $wtbanner->active);
		else
			$active = 1;
		$helper->fields_value['active'] = $active;
		if ($id_wtbanner)
		{
			$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_wtbanner');
			$helper->fields_value['id_wtbanner'] = (int)Tools::getValue('id_wtbanner', $wtbanner->id_wtbanner);	
		}
		$this->html .= $helper->generateForm($this->fields_form);
	}
	
	public function getBannersDisplay($hookname)
	{
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		$banners = Db::getInstance()->ExecuteS(
		'SELECT bs.*, bl.`title`,bl.`link`,bl.`text`,b.`file_name`
			FROM `'._DB_PREFIX_.'wtbanner` b
			LEFT JOIN `'._DB_PREFIX_.'wtbanner_shop` bs ON (bs.`id_wtbanner` = b.`id_wtbanner` )
			LEFT JOIN `'._DB_PREFIX_.'wtbanner_lang` bl ON (b.`id_wtbanner` = bl.`id_wtbanner` AND bl.`id_shop` = '.$id_shop.') 
			WHERE bl.id_lang = '.(int)$id_lang.' AND bs.id_shop = '.(int)$id_shop.' AND bs.`active` = 1');
		return $banners;
	}
	
	public function getContentForHook($hookname)
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$banners = $this->getBannersDisplay($hookname);
		if (!empty($banners))
		{
			$this->context->smarty->assign(array(
				'banners' => $banners,
				'banner_config' => $this->config,
			));
			return $this->display(__FILE__, 'wtbanner.tpl');
		}
	}
	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'views/css/wtbanner.css', 'all');
	}
	public function hookDisplayTopColumn()
	{
		return $this->getContentForHook('displayTopColumn');
	}
	public function hookDisplayHome()
	{
		return $this->getContentForHook('displayHome');
	}
	public function hookDisplayTopHome()
	{
		return $this->hookDisplayHome();
	}
	public function hookDisplayBottomHome()
	{
		return $this->hookDisplayHome();
	}
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wtbanner_shop (`id_wtbanner`, `id_shop`, `active`)
		SELECT id_wtbanner, '.(int)$params['new_id_shop'].', active
		FROM '._DB_PREFIX_.'wtbanner_shop
		WHERE id_shop = '.(int)$params['old_id_shop']);
		
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wtbanner_lang (`id_wtbanner`, `id_lang`, `id_shop`, `title`, `link`, `text`)
		SELECT id_wtbanner,id_lang, '.(int)$params['new_id_shop'].', title, link, text 
		FROM '._DB_PREFIX_.'wtbanner_lang
		WHERE id_shop = '.(int)$params['old_id_shop']);
	}
	public function hookActionObjectLanguageAddAfter($params)
	{	
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wtbanner_lang (`id_wtbanner`, `id_lang`, `id_shop`, `title`, `link`, `text`)
		SELECT id_wtbanner, '.(int)$params['object']->id.', id_shop, title, link, text 
		FROM '._DB_PREFIX_.'wtbanner_lang
		WHERE id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT'));
	}
}