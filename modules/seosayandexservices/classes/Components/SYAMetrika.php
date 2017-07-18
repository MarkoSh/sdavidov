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
 * Class SYAMetrika
 */
class SYAMetrika extends SYAComponent
{
	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displayHeader')
		&& $this->registerHook('actionCustomerAccountAdd')
		&& $this->registerHook('displayFooter')
		&& $this->registerHook('displayOrderConfirmation');
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
			'METRIKA_COUNTER' => '',
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
				'counter' => (string)SYAConfigurationTools::get('METRIKA_COUNTER'),
		);
	}

	/**
	 * @param $type
	 * @param null $params
	 */
	public function goal($type, $params = null)
	{
		$goals = $this->getGoals();

		$goal = array(
			'type' => $type
		);
		if ($params)
			$goal['params'] = $params;

		$goals[] = $goal;

		$this->context->cookie->sya_metrika_goals = Tools::jsonEncode($goals);
		$this->context->cookie->write();
	}

	/**
	 * @return array
	 */
	protected function getGoals()
	{
		if ($this->context->cookie->sya_metrika_goals)
		{
			try
			{
				$goals = Tools::jsonDecode($this->context->cookie->sya_metrika_goals, true);
				if (!is_array($goals))
					$goals = array();

				return $goals;
			}
			catch (\Exception $e)
			{
				return array();
			}
		}

		return array();
	}

	/**
	 * @return array
	 */
	protected function cleanGoal()
	{
		$goals = $this->getGoals();

		$this->context->cookie->sya_metrika_goals = Tools::jsonEncode(array());
		$this->context->cookie->write();

		return $goals;
	}

	/**
	 * @return string
	 */
	public function hookDisplayFooter()
	{
		SeoSAYandexServices::registerSmartyFunctions();

		$config = $this->getConfiguration();
		$this->context->smarty->assign('sya_metrika_counter', $config['counter']);
		unset($config['counter']);
		$this->context->smarty->assign('sya_metrika_config', $config);
		$this->context->smarty->assign('sya_goals', $this->cleanGoal());

		return $this->render($this->getFrontTemplatePath('initializer.tpl'));
	}

	/**
	 * @void
	 */
	public function hookDisplayHeader()
	{
		$this->context->controller->addJS(
			$this->module->getPathUri().'/views/js/front/components/metrika/yandex-metrika.js'
		);
	}

	/**
	 * @param array $params
	 *
	 * @void
	 */
	public function hookActionCustomerAccountAdd($params)
	{
		$opc = (bool)Configuration::get('PS_ORDER_PROCESS_TYPE');
		if ($opc && Tools::getValue('ajax') && Tools::isSubmit('submitAccount'))
			return;

		$customer = array_key_exists('newCustomer', $params) ? $params['newCustomer'] : null;
		$params = null;
		if ($customer instanceof Customer)
		{
			$params['First name'] = $customer->firstname;
			$params['Last name'] = $customer->lastname;
		}

		self::goal('create_account', $params);
	}

	/**
	 * @param array $hook
	 *
	 * @void
	 */
	public function hookDisplayOrderConfirmation($hook)
	{
		$order = array_key_exists('objOrder', $hook) ? $hook['objOrder'] : null;
		$params = null;
		if ($order instanceof Order)
		{
			$total_to_pay = array_key_exists('total_to_pay', $hook) ?
					(float)$params['total_to_pay'] :
					$order->getOrdersTotalPaid();

			$params['Order ID'] = (string)$order->id;
			$params['Total paid'] = (string)$total_to_pay;
			$params['Currency'] = Tools::strtoupper($this->context->currency->iso_code);

			$products = array();
			foreach ($order->getProducts() as $product)
			{
				$products[] = array(
					'id' => (string)$product['product_id'],
					'name' => (string)$product['product_name'],
					'price' => (string)$product['product_price']
				);
			}

			$params['Products'] = $products;
		}

		self::goal('order', $params);
	}
}