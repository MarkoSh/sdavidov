<?php
/**
 * 2007-2017 PrestaShop
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
 *  @author    SeoSA<885588@bk.ru>
 *  @copyright 2012-2017 SeoSA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class SYAComponentFrontController
 */
class SYAComponentFrontController extends ModuleFrontController
{
	/**
	 * @var SeoSAYandexServices
	 */
	public $module;

	/**
	 * @var SYAComponent
	 */
	public $component;

	/**
	 * SYAComponentFrontController constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->component = $this->module->getComponent(Tools::getValue('component'));
	}

	/**
	 * Get path to front office templates for the module
	 * @param string $template
	 *
	 * @return string
	 */
	public function getTemplatePath($template)
	{
		$theme_path = _PS_THEME_DIR_.'modules/'.$this->module->name.
				'/views/templates/front/components/'.$this->component->getName().'/'.$template;
		if (Tools::file_exists_cache($theme_path))
			return $theme_path;

		$module_path = _PS_MODULE_DIR_.$this->module->name.
				'/views/templates/front/components/'.$this->component->getName().'/'.$template;
		if (Tools::file_exists_cache($module_path))
			return $module_path;

		return false;
	}

	public function setTemplate($template, $params = array(), $locale = null)
	{
		if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
		{
			if (!array_key_exists('renderTemplate', $this->context->smarty->registered_plugins['function']))
				smartyRegisterFunction($this->context->smarty, 'function', 'renderTemplate', array($this, 'renderTemplate'));
			if (!array_key_exists('displayPrice', $this->context->smarty->registered_plugins['function']))
				smartyRegisterFunction($this->context->smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
			$this->context->smarty->assign(array(
				'template_path' => $this->getTemplatePath($template),
				'navigationPipe' => '>',
				'use_taxes' => (int)Configuration::get('PS_TAX')
			));
			$template = 'module:'.$this->module->name.'/views/templates/front/base_17.tpl';
		}
		$this->context->smarty->assign(array(
			'is_17' => version_compare(_PS_VERSION_, '1.7.0.0', '>=')
		));
		parent::setTemplate($template, $params, $locale);
	}

	/**
	 * @param $smarty
	 * @return mixed
	 */
	public function renderTemplate($smarty)
	{
		$file = $smarty['file'];
		return $this->context->smarty->fetch($file);
	}
}