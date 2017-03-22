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

if (!defined('_PS_VERSION_'))
	exit;

class WtBannerSpecial extends Module
{
	public function __construct()
	{
		$this->name = 'wtbannerspecial';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'waterthemes';
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('WT Banner Special');
		$this->description = $this->l('Displays a banner popup');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return
			parent::install() &&
			$this->registerHook('displayBanner') &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('actionObjectLanguageAddAfter') &&
			$this->installFixtures() &&
			$this->disableDevice(Context::DEVICE_MOBILE);
	}

	public function hookActionObjectLanguageAddAfter($params)
	{
		return $this->installFixture((int)$params['object']->id, Configuration::get('WTBANNERSPECIAL_IMG', (int)Configuration::get('PS_LANG_DEFAULT')));
	}

	protected function installFixtures()
	{
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang)
			$this->installFixture((int)$lang['id_lang'], '374fb25a2bcb15b8ebd89e334229efd0.png');
		Configuration::updateValue('WTBANNERSPECIAL_COOKIESTIME', 3600);
		return true;
	}

	protected function installFixture($id_lang, $image = null)
	{
		$values = array();
		$values['WTBANNERSPECIAL_IMG'][(int)$id_lang] = $image;
		$values['WTBANNERSPECIAL_LINK'][(int)$id_lang] = '';
		$values['WTBANNERSPECIAL_DESC'][(int)$id_lang] = '';
		Configuration::updateValue('WTBANNERSPECIAL_IMG', $values['WTBANNERSPECIAL_IMG']);
		Configuration::updateValue('WTBANNERSPECIAL_LINK', $values['WTBANNERSPECIAL_LINK']);
		Configuration::updateValue('WTBANNERSPECIAL_DESC', $values['WTBANNERSPECIAL_DESC']);
	}

	public function uninstall()
	{
		Configuration::deleteByName('WTBANNERSPECIAL_IMG');
		Configuration::deleteByName('WTBANNERSPECIAL_LINK');
		Configuration::deleteByName('WTBANNERSPECIAL_DESC');
		Configuration::deleteByName('WTBANNERSPECIAL_COOKIESTIME');
		return parent::uninstall();
	}
	public function hookDisplayTop()
	{
		if (!$this->isCached('wtbannerspecial.tpl', $this->getCacheId()))
		{
			$imgname = Configuration::get('WTBANNERSPECIAL_IMG', $this->context->language->id);

			if ($imgname && file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'views/img'.DIRECTORY_SEPARATOR.$imgname))
				$this->smarty->assign('banner_img', $this->context->link->protocol_content.Tools::getMediaServer($imgname).$this->_path.'views/img/'.$imgname);

			$this->smarty->assign(array(
				'banner_link' => Configuration::get('WTBANNERSPECIAL_LINK', $this->context->language->id),
				'banner_desc' => Configuration::get('WTBANNERSPECIAL_DESC', $this->context->language->id),
				'cookies_time' => Configuration::get('WTBANNERSPECIAL_COOKIESTIME')
			));
		}

		return $this->display(__FILE__, 'wtbannerspecial.tpl', $this->getCacheId());
	}

	public function hookDisplayBanner()
	{
		return $this->hookDisplayTop();
	}

	public function hookDisplayFooter()
	{
		return $this->hookDisplayTop();
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/wtbannerspecial.css', 'all');
		$this->context->controller->addJS($this->_path.'views/js/wtbannerspecial.js');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			$languages = Language::getLanguages(false);
			$values = array();
			$update_images_values = false;

			foreach ($languages as $lang)
			{
				if (isset($_FILES['WTBANNERSPECIAL_IMG_'.$lang['id_lang']])
					&& isset($_FILES['WTBANNERSPECIAL_IMG_'.$lang['id_lang']]['tmp_name'])
					&& !empty($_FILES['WTBANNERSPECIAL_IMG_'.$lang['id_lang']]['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['WTBANNERSPECIAL_IMG_'.$lang['id_lang']], 4000000))
						return $error;
					else
					{
						$ext = Tools::substr($_FILES['WTBANNERSPECIAL_IMG_'.$lang['id_lang']]['name'], strrpos($_FILES['WTBANNERSPECIAL_IMG_'
						.$lang['id_lang']]['name'], '.') + 1);
						$file_name = md5($_FILES['WTBANNERSPECIAL_IMG_'.$lang['id_lang']]['name']).'.'.$ext;

						if (!move_uploaded_file($_FILES['WTBANNERSPECIAL_IMG_'.$lang['id_lang']]['tmp_name'], dirname(__FILE__)
						.DIRECTORY_SEPARATOR.'views/img'.DIRECTORY_SEPARATOR.$file_name))
							return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
						else
						{
							if (Configuration::hasContext
							('WTBANNERSPECIAL_IMG', $lang['id_lang'], Shop::getContext()) && Configuration::get('WTBANNERSPECIAL_IMG', $lang['id_lang']) != $file_name)
								unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'views/img'.DIRECTORY_SEPARATOR.Configuration::get('WTBANNERSPECIAL_IMG', $lang['id_lang']));
							$values['WTBANNERSPECIAL_IMG'][$lang['id_lang']] = $file_name;
						}
					}

					$update_images_values = true;
				}
				$values['WTBANNERSPECIAL_LINK'][$lang['id_lang']] = Tools::getValue('WTBANNERSPECIAL_LINK_'.$lang['id_lang']);
				$values['WTBANNERSPECIAL_DESC'][$lang['id_lang']] = Tools::getValue('WTBANNERSPECIAL_DESC_'.$lang['id_lang']);
			}

			if ($update_images_values)
				Configuration::updateValue('WTBANNERSPECIAL_IMG', $values['WTBANNERSPECIAL_IMG']);

			Configuration::updateValue('WTBANNERSPECIAL_LINK', $values['WTBANNERSPECIAL_LINK']);
			Configuration::updateValue('WTBANNERSPECIAL_DESC', $values['WTBANNERSPECIAL_DESC'], true);
			Configuration::updateValue('WTBANNERSPECIAL_COOKIESTIME', Tools::getValue('WTBANNERSPECIAL_COOKIESTIME'));
			$this->_clearCache('wtbannerspecial.tpl');
			return $this->displayConfirmation($this->l('The settings have been updated.'));
		}
		return '';
	}

	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'file_lang',
						'label' => $this->l('Banner image'),
						'name' => 'WTBANNERSPECIAL_IMG',
						'desc' => $this->l('Upload an image for your banner'),
						'lang' => true,
					),
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Banner Link'),
						'name' => 'WTBANNERSPECIAL_LINK',
						'desc' => $this->l('Enter the link associated to your banner.
						When clicking on the banner, the link opens in the same window. If no link is entered, it redirects to the homepage.')
					),
					array(
						'type' => 'textarea',
						'lang' => true,
						'label' => $this->l('Banner HTML'),
						'name' => 'WTBANNERSPECIAL_DESC',
						'autoload_rte' => true,
						'desc' => $this->l('Please enter a for the banner.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Cookies time'),
						'name' => 'WTBANNERSPECIAL_COOKIESTIME',
						'desc' => $this->l('Enter the time(second) number. After this time, your banner open again when you to the homepage.')
					)
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->module = $this;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitStoreConf';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).
		'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array();

		foreach ($languages as $lang)
		{
			$fields['WTBANNERSPECIAL_IMG'][$lang['id_lang']] = Tools::getValue('WTBANNERSPECIAL_IMG_'
			.$lang['id_lang'], Configuration::get('WTBANNERSPECIAL_IMG', $lang['id_lang']));
			$fields['WTBANNERSPECIAL_LINK'][$lang['id_lang']] = Tools::getValue('WTBANNERSPECIAL_LINK_'
			.$lang['id_lang'], Configuration::get('WTBANNERSPECIAL_LINK', $lang['id_lang']));
			$fields['WTBANNERSPECIAL_DESC'][$lang['id_lang']] = Tools::getValue('WTBANNERSPECIAL_DESC_'
			.$lang['id_lang'], Configuration::get('WTBANNERSPECIAL_DESC', $lang['id_lang']));
		}
		$fields['WTBANNERSPECIAL_COOKIESTIME'] = Tools::getValue('WTBANNERSPECIAL_COOKIESTIME', Configuration::get('WTBANNERSPECIAL_COOKIESTIME'));
		return $fields;
	}
}