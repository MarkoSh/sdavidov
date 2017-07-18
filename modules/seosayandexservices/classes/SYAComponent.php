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
 * Class SYAComponent
 */
abstract class SYAComponent
{
	/**
	 * @var SeoSAYandexServices
	 */
	protected $module;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $display_name;

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var Smarty|Smarty_data
	 */
	protected $smarty;

	/**
	 * SYAComponent constructor.
	 * @param SeoSAYandexServices $module
	 * @param Context|null $context
	 */
	public function __construct(SeoSAYandexServices $module, Context $context = null)
	{
		$this->module = $module;
		$this->name = str_replace('sya', '', Tools::strtolower(get_class($this)));
		$this->display_name = $this->name;

		$this->context = $context ? $context : Context::getContext();
		$this->smarty = $this->context->smarty;
	}

	/**
	 * @return mixed|string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->display_name;
	}

	/**
	 * @return bool
	 */
	public function install()
	{
		return $this->installConfiguration();
	}

	/**
	 * @return bool
	 */
	public function uninstall()
	{
		return $this->uninstallConfiguration();
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array();
	}

	/**
	 * @return bool
	 */
	public function installConfiguration()
	{
		SYAConfigurationTools::update(
			Tools::strtoupper($this->getName()).'_ENABLED',
			$this->isEnabledByDefault()
		);

		foreach ($this->getDefaults() as $key => $value)
			SYAConfigurationTools::update($key, $value);

		return true;
	}

	/**
	 * @return bool
	 */
	public function uninstallConfiguration()
	{
		SYAConfigurationTools::delete(
			Tools::strtoupper($this->getName()).'_ENABLED'
		);

		foreach ($this->getDefaults() as $key => $value)
		{
			unset($value);
			SYAConfigurationTools::delete($key);
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function hasWelcome()
	{
		return $this->angularTemplateExists('welcome.tpl');
	}

	/**
	 * @return bool
	 */
	public function hasNavigationEntry()
	{
		return $this->hasConfigForm();
	}

	/**
	 * @return array
	 */
	public function getNavigationEntry()
	{
		return array(
				'route' => 'configure-'.$this->getName(),
				'name' => Tools::ucfirst($this->getName()),
		);
	}

	/**
	 * @return bool
	 */
	public function hasConfigForm()
	{
		return $this->angularTemplateExists('configure/form.tpl');
	}

	/**
	 * @return bool
	 */
	public function hasConfigFormLeft()
	{
		return $this->angularTemplateExists('configure/left-form.tpl');
	}

	/**
	 * @param $template_path
	 *
	 * @return bool
	 */
	public function templateExists($template_path)
	{
		return file_exists($this->getAdminTemplatePath($template_path));
	}

	/**
	 * @param $template_path
	 *
	 * @return bool
	 */
	public function angularTemplateExists($template_path)
	{
		return file_exists($this->getAngularTemplatePath($template_path));
	}

	/**
	 * @param $template_path
	 * @return string
	 */
	protected function getAdminTemplatePath($template_path)
	{
		return dirname(__FILE__).'/../views/templates/admin/components/'.$this->name.'/'.$template_path;
	}

	/**
	 * @param $template_path
	 * @return string
	 */
	protected function getFrontTemplatePath($template_path)
	{
		return dirname(__FILE__).'/../views/templates/front/components/'.$this->name.'/'.$template_path;
	}

	/**
	 * @param $template_path
	 * @return string
	 */
	protected function getAngularTemplatePath($template_path)
	{
		return dirname(__FILE__).'/../views/templates/admin/angular-templates/components/'.$this->name.'/'.$template_path;
	}

	/**
	 * @param $template_path
	 * @return string
	 * @throws Exception
	 * @throws SmartyException
	 */
	protected function render($template_path)
	{
		return $this->smarty->fetch($template_path);
	}

	/**
	 * @return array
	 */
	protected function getAngularRoutes()
	{
		return array();
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return array(
				'name' => $this->getName(),
				'angular_routes' => $this->getAngularRoutes(),
				'has_welcome' => $this->hasWelcome(),
				'has_config_form' => $this->hasConfigForm(),
				'has_config_form_left' => $this->hasConfigFormLeft(),
				'has_navigation_entry' => $this->hasNavigationEntry(),
				'enabled' => $this->isEnabled(),
		);
	}

	/**
	 * @param string $hook
	 * @return bool
	 */
	public function registerHook($hook)
	{
		return $this->module->registerComponentHook($this, $hook);
	}


	/**
	 * @param $string
	 * @return string
	 */
	public function l($string)
	{
		return Translate::getModuleTranslation($this->module, $string, Tools::strtolower(get_class($this)));
	}

	/**
	 * @return array
	 */
	public function getAngularValues()
	{
		return array(
			$this->getName().'_config' => $this->getConfiguration(),
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array();
	}

	/**
	 * @return bool
	 */
	public function enable()
	{
		return (bool)SYAConfigurationTools::update(
			Tools::strtoupper($this->getName()).'_ENABLED',
			true
		);
	}

	/**
	 * @return bool
	 */
	public function disable()
	{
		return (bool)SYAConfigurationTools::update(
			Tools::strtoupper($this->getName()).'_ENABLED',
			false
		);
	}

	/**
	 * @return bool
	 */
	public function isEnabled()
	{
		return (bool)SYAConfigurationTools::get(
				Tools::strtoupper($this->getName()).'_ENABLED'
		);
	}
	/**
	 * @return bool
	 */
	public function isEnabledByDefault()
	{
		return false;
	}

}