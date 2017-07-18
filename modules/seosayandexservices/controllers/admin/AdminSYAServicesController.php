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
 * Class AdminSYAServicesController
 */
class AdminSYAServicesController extends ModuleAdminController
{
	/**
	 * @var SeoSAYandexServices
	 */
	public $module;

	/**
	 * AdminSYAServicesController constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->bootstrap = true;

		SeoSAYandexServices::registerAutoloader();
		SeoSAYandexServices::registerSmartyFunction();
	}

	/**
	 * {@inheritdoc}
	 */
	public function initContent()
	{
		$this->show_toolbar = false;
		$this->display = 'view';

		parent::initContent();
	}

	/**
	 * @return string
	 * @throws Exception
	 * @throws SmartyException
	 */
	public function renderView()
	{
		$this->module->loadAngularApp();

		$documentation_folder = $this->module->getLocalPath().'views/templates/admin/documentation';
		$documentation_pages = SYATools::globRecursive($documentation_folder.'/**.tpl');
		natsort($documentation_pages);
		$this->context->smarty->assign('documentation_pages', $documentation_pages);
		$this->context->smarty->assign('documentation_folder', $documentation_folder);

		return $this->context->smarty->fetch(
			$this->module->getLocalPath().'views/templates/admin/configure.tpl'
		);
	}

	/**
	 * @return bool
	 */
	public function postProcess()
	{
		if ($this->ajax)
		{
			$action = Tools::getValue('action');

			if ($action)
			{
				$component = Tools::getValue('component');
				if (Tools::isSubmit('component'))
					$component = $this->module->getComponent($component);

				$method = 'ajaxProcess'.Tools::toCamelCase($action, true);

				$callee = null;
				if ($component)
					$callee = method_exists($component, $method) ? array($component, $method) : null;
				else
					$callee = method_exists($this, $method) ? array($this, $method) : null;

				if ($callee)
				{
					try
					{
						SYATools::jsonResponse(call_user_func($callee));
					}
					catch (Exception $e)
					{
						SYATools::jsonErrorResponse(array(
								'error' => $e->getMessage()
						));
					}
				}

				SYATools::jsonErrorResponse($this->l('Unknown method'));
			}
		}

		return parent::postProcess();
	}

	/**
	 * @return array
	 */
	protected function ajaxProcessProductsSearch()
	{
		return $this->processAjaxSearch('products');
	}

	/**
	 * @return array
	 */
	protected function ajaxProcessCategoriesSearch()
	{
		return $this->processAjaxSearch('categories');
	}

	/**
	 * @return array
	 */
	protected function ajaxProcessManufacturersSearch()
	{
		return $this->processAjaxSearch('manufacturers');
	}

	/**
	 * @return array
	 */
	protected function ajaxProcessSuppliersSearch()
	{
		return $this->processAjaxSearch('suppliers');
	}

	/**
	 * @param string $type
	 * @return mixed
	 */
	protected function processAjaxSearch($type)
	{
		$finder = new SYAObjectFinder();

		$method = 'find'.Tools::toCamelCase($type, true);

		return $finder->{$method}(Tools::getValue('query'));
	}

	/**
	 * @return mixed
	 */
	protected function ajaxProcessUpdateConfigurationValue()
	{
		$name = Tools::strtoupper(SYAJSONRequest::getValue('name'));
		$value = SYAJSONRequest::getValue('value');
		$html = (bool)SYAJSONRequest::getValue('html');
		$escape = (bool)SYAJSONRequest::getValue('escape');
		if ($escape)
			$value = htmlspecialchars($value);
		if (is_array($value))
		{
			$value = Tools::jsonEncode($value);
			$html = true;
		}

		return SYAConfigurationTools::update($name, $value, $html);
	}

	/**
	 * @return mixed
	 */
	protected function ajaxProcessEnableComponent()
	{
		$component = $this->module->getComponent(
			SYAJSONRequest::getValue('component')
		);

		return $component->enable();
	}

	/**
	 * @return mixed
	 */
	protected function ajaxProcessDisableComponent()
	{
		$component = $this->module->getComponent(
			SYAJSONRequest::getValue('component')
		);

		return $component->disable();
	}

	/**
	 * @return mixed
	 */
	protected function ajaxProcessUpdateConfigurationMultiple()
	{
		$configuration = SYAJSONRequest::getValue('configuration');
		$html = (bool)SYAJSONRequest::getValue('html');
		$escape = (bool)SYAJSONRequest::getValue('escape');

		if (is_array($configuration) && $configuration)
		{
			foreach ($configuration as $name => $value)
			{
				if ($escape)
					$value = htmlspecialchars($value);
				SYAConfigurationTools::update(Tools::strtoupper($name), $value, $html);
			}
		}

		return true;
	}

	/**
	 * @return mixed
	 */
	protected function ajaxProcessUpdateProductsFilterConfigurationValue()
	{
		$name = Tools::strtoupper(SYAJSONRequest::getValue('name'));
		$type = SYAJSONRequest::getValue('mode');
		$items = SYAJSONRequest::getValue('items');
		SYATools::arrayOfIds($items);

		return SYAConfigurationTools::update($name.'_MODE', $type)
		&& SYAConfigurationTools::update($name.'_ITEMS', $items ? implode(',', $items) : '');
	}
}