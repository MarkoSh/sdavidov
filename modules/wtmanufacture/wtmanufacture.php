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

include_once _PS_MODULE_DIR_.'wtmanufacture/classes/WtManuClass.php';
include_once _PS_MODULE_DIR_.'wtmanufacture/sql/SampleDataManu.php';

class WtManufacture extends Module
{
	private $temp_url = '{wtmanufacture_url}';
	private $html;
	private $settings_default;
	private $wt_manu_config;
	private $config;
	public function __construct()
	{
		$this->name = 'wtmanufacture';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'waterthemes';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('WT Manufacture Block');
		$this->description = $this->l('Show Manufacture and banner');
		$this->settings_default = array (
			'used_slider' => 1,
			'number_banner_aline' => 4
		);
		$this->wt_manu_config = 'WT_CONFIG_MANUFACTURE';
		$this->getInitSettings();
	}
	public function getInitSettings()
	{
		$this->config = (array)Tools::jsonDecode(Configuration::get($this->wt_manu_config));
		$this->config = (object)array_merge((array)$this->settings_default, $this->config);
	}
	public function install()
	{
		if (!parent::install() || !$this->registerHook('actionShopDataDuplication') || !$this->registerHook('displayHeader') || !$this->registerHook('displayBottomHome') || !$this->registerHook('actionObjectLanguageAddAfter'))
			return false;
		if (!Configuration::hasKey($this->wt_manu_config))
			Configuration::updateValue($this->wt_manu_config, '');
		include(dirname(__FILE__).'/sql/install.php');
		$sample_data = new SampleDataManu();
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
	
	public function getPositionList()
	{
		$position_line = array();
		$position_line[0]['position'] = 'left';
		$position_line[1]['position'] = 'right';
		return $position_line;
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
			if ((!isset($_FILES['image']['tmp_name']) || $_FILES['image']['tmp_name'] == '') && !Tools::getValue('id_wtmanufacture'))
				$this->html .= $this->displayError($this->l('Banner empty !'));
			else if (Tools::getValue('title_'.$languageDefault.'') == '')
				$this->html .= $this->displayError($this->l('Title for language default empty !'));
			else 
			{
				$reinsurance = new WtManuClass(Tools::getValue('id_wtmanufacture'));
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
		elseif (Tools::isSubmit('changeStatus') && Tools::getValue('id_wtmanufacture'))
		{
			$banner = new WtManuClass(Tools::getValue('id_wtmanufacture'));
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
					Configuration::updateValue($this->wt_manu_config, $config);
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&successConfirmation');
				}
			}
		}
		else if (Tools::isSubmit('deleteBanner'))
		{
			$banner = new WtManuClass(Tools::getValue('id_wtmanufacture'));
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
			FROM `'._DB_PREFIX_.'wtmanufacture` b
			LEFT JOIN `'._DB_PREFIX_.'wtmanufacture_shop` bs ON (bs.`id_wtmanufacture` = b.`id_wtmanufacture` )
			LEFT JOIN `'._DB_PREFIX_.'wtmanufacture_lang` bl ON (b.`id_wtmanufacture` = bl.`id_wtmanufacture` '.( $id_shop ? 'AND bl.`id_shop` = '.$id_shop : ' ' ).') 
			WHERE bl.id_lang = '.(int)$id_lang.
			($active ? ' AND bs.`active` = 1' : ' ').
			( $id_shop ? 'AND bs.`id_shop` = '.$id_shop : ' ' ).''))
			return false;
		return $result;
	}
	public function displaySettings()
	{
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
				$banners[$key]['status'] = $this->displayStatus($banner['id_wtmanufacture'], $banner['active']);
		}
		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'banners' => $banners
			)
		);
		return $this->display(__FILE__, 'views/templates/admin/list.tpl');
	}
	
	public function displayStatus($id_wtmanufacture, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_wtmanufacture='.(int)$id_wtmanufacture.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}
	
	public function initForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$id_wtmanufacture = Tools::getValue('id_wtmanufacture');
		if ($id_wtmanufacture)
			$wtmanufacture = new WtManuClass((int)$id_wtmanufacture);
		else
			$wtmanufacture = new WtManuClass();
		if ($wtmanufacture->file_name != '')
			$banner = __PS_BASE_URI__.'modules/'.$this->name.'/views/img/'.$wtmanufacture->file_name;
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
					'type' => 'select',
					'label' => $this->l('Position'),
					'name' => 'position',
					'options' => array(
						'query' => $this->getPositionList(),
						'id' => 'position',
						'name' => 'position'
						)
					),
				array(
					'type' => 'textarea',
					'label' => $this->l('Text:'),
					'lang' => true,
					'name' => 'text',
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
		$helper->name_controller = 'wtmanufacture';
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
		
		if ($id_wtmanufacture)
		{
			$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_wtmanufacture');
			$helper->fields_value['id_wtmanufacture'] = (int)Tools::getValue('id_wtmanufacture', $wtmanufacture->id_wtmanufacture);	
			$helper->fields_value['active'] = Tools::getValue('active', $wtmanufacture->active);
			$helper->fields_value['position'] = Tools::getValue('position', $wtmanufacture->position);
			foreach (Language::getLanguages(false) as $lang)
			{
				$helper->fields_value['title'][(int)$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $wtmanufacture->title[(int)$lang['id_lang']]);
				$helper->fields_value['link'][(int)$lang['id_lang']] = Tools::getValue('link_'.(int)$lang['id_lang'], $wtmanufacture->link[(int)$lang['id_lang']]);
				$helper->fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], $wtmanufacture->text[(int)$lang['id_lang']]);
			}
		}
		else
		{
			$helper->fields_value['active'] = Tools::getValue('active', 1);
			$helper->fields_value['position'] = Tools::getValue('position', 'left');
			foreach (Language::getLanguages(false) as $lang)
			{
				$helper->fields_value['title'][(int)$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], 'Banner Title');
				$helper->fields_value['link'][(int)$lang['id_lang']] = Tools::getValue('link_'.(int)$lang['id_lang'], '#');
				$helper->fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], '');
			}
			
		}
		$this->html .= $helper->generateForm($this->fields_form);
	}
	
	public function getBannersDisplay($position)
	{
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		$banners = Db::getInstance()->ExecuteS(
		'SELECT bs.*, bl.`title`,bl.`link`,bl.`text`,b.`file_name`
			FROM `'._DB_PREFIX_.'wtmanufacture` b
			LEFT JOIN `'._DB_PREFIX_.'wtmanufacture_shop` bs ON (bs.`id_wtmanufacture` = b.`id_wtmanufacture` )
			LEFT JOIN `'._DB_PREFIX_.'wtmanufacture_lang` bl ON (b.`id_wtmanufacture` = bl.`id_wtmanufacture` AND bl.`id_shop` = '.$id_shop.') 
			WHERE bs.position = "'.$position.'" AND bl.id_lang = '.(int)$id_lang.' AND bs.id_shop = '.(int)$id_shop.' AND bs.`active` = 1');
		return $banners;
	}
	
	public function getContentForHook()
	{
		$this->context = Context::getContext();
		$manufacturers = Manufacturer::getManufacturers(false, 0, true);
		if (!empty($manufacturers))
		{
			$banners_l = $this->getBannersDisplay('left');
			$banners_r = $this->getBannersDisplay('right');
			$this->context->smarty->assign(array(
				'banners_l' => $banners_l,
				'banners_r' => $banners_r,
				'cs_config' => $this->config,
				'manufacs' => $manufacturers,
				'ps_manu_img_dir' => _PS_MANU_IMG_DIR_
			));
			return $this->display(__FILE__, 'wtmanufacture.tpl');
		}
	}
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/wtmanufacture.css', 'all');
	}
	public function hookDisplayTopColumn()
	{
		return $this->getContentForHook();
	}
	public function hookDisplayHome()
	{
		return $this->getContentForHook();
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
		INSERT IGNORE INTO '._DB_PREFIX_.'wtmanufacture_shop (`id_wtmanufacture`, `id_shop`, `position`, `active`)
		SELECT id_wtmanufacture, '.(int)$params['new_id_shop'].', position, active
		FROM '._DB_PREFIX_.'wtmanufacture_shop
		WHERE id_shop = '.(int)$params['old_id_shop']);
		
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wtmanufacture_lang (`id_wtmanufacture`, `id_lang`, `id_shop`, `title`, `link`, `text`)
		SELECT id_wtmanufacture,id_lang, '.(int)$params['new_id_shop'].', title, link, text 
		FROM '._DB_PREFIX_.'wtmanufacture_lang
		WHERE id_shop = '.(int)$params['old_id_shop']);
	}
	public function hookActionObjectLanguageAddAfter($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wtmanufacture_lang (`id_wtmanufacture`, `id_lang`, `id_shop`, `title`, `link`, `text`)
		SELECT id_wtmanufacture, '.(int)$params['object']->id.', id_shop, title, link, text 
		FROM '._DB_PREFIX_.'wtmanufacture_lang
		WHERE id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT'));
	}
}