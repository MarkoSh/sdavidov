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

class SeoSAYandexServicesFrontModuleFrontController
{
	/** @var ModuleFrontController */
	public $real_controller;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		/** @var SeoSAYandexServices $module */
		$module = Module::getInstanceByName('seosayandexservices');
		$module->registerAutoloader();
		$module->registerSmartyFunction();

		$component_name = Tools::getValue('component');
		$component = $module->getComponent($component_name);
		if ($component)
		{
			$component_controller = Tools::getValue('component_controller');
			$class = 'SYA'.Tools::toCamelCase($component->getName(), true).
					Tools::toCamelCase($component_controller, true).'Controller';

			if ($component_controller && class_exists($class))
				$this->real_controller = new $class();
		}

		if (!$this->real_controller)
			Tools::redirect(Context::getContext()->link->getPageLink('notfound'));
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->real_controller, $name), $arguments);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->real_controller->{$name};
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		return $this->real_controller->{$name} = $value;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->real_controller->{$name});
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function __unset($name)
	{
		unset($this->real_controller->{$name});
	}
}