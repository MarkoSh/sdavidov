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
 * Class SYAOrderMarket
 */
class SYAOrderMarket extends SYAComponent
{
	const STATUS_DELIVERY = 900;
	const STATUS_CANCELLED = 901;
	const STATUS_PICKUP = 902;
	const STATUS_PROCESSING = 903;
	const STATUS_DELIVERED = 904;
	const STATUS_MAKEORDER = 905;
	const STATUS_UNPAID = 906;
	const STATUS_RESERVATION_EXPIRED = 907;
	const STATUS_RESERVATION = 908;

	public $order_states = array();

	/**
	 * SYAMarket constructor.
	 *
	 * @param SeoSAYandexServices $module
	 * @param Context|null $context
	 */
	public function __construct(SeoSAYandexServices $module, Context $context = null)
	{
		parent::__construct($module, $context);

		$this->display_name = $this->l('Order on Yandex.Market');

		$this->order_states = array(
			self::STATUS_DELIVERY => array(
				'name' => array(
					'en' => 'Yandex Status: Delivery',
					'ru' => 'Яндекс Статус: Доставка'
				),
				'color' => '#8A2BE2',
				'id' => self::STATUS_DELIVERY,
				'paid' => true,
				'shipped' => false,
				'logable' => true,
				'delivery' => true
			),
			self::STATUS_CANCELLED => array(
				'name' => array(
					'en' => 'Yandex Status: Cancelled',
					'ru' => 'Яндекс Статус: Отменен'
				),
				'color' => '#b70038',
				'id' => self::STATUS_CANCELLED,
				'paid' => false,
				'shipped' => false,
				'logable' => true,
				'delivery' => false
			),
			self::STATUS_PICKUP => array(
				'name' => array(
					'en' => 'Yandex Status: Pickup',
					'ru' => 'Яндекс Статус: Самовывоз'
				),
				'color' => '#cd98ff',
				'id' => self::STATUS_PICKUP,
				'paid' => true,
				'shipped' => true,
				'logable' => true,
				'delivery' => true
			),
			self::STATUS_PROCESSING => array(
				'name' => array(
					'en' => 'Yandex Status: Processing',
					'ru' => 'Яндекс Статус: В процессе'
				),
				'color' => '#FF8C00',
				'id' => self::STATUS_PROCESSING,
				'paid' => true,
				'shipped' => false,
				'logable' => false,
				'delivery' => true
			),
			self::STATUS_DELIVERED => array(
				'name' => array(
					'en' => 'Yandex Status: Delivered',
					'ru' => 'Яндекс Статус: Доставлен'
				),
				'color' => '#108510',
				'id' => self::STATUS_DELIVERED,
				'paid' => true,
				'shipped' => true,
				'logable' => true,
				'delivery' => true
			),
			self::STATUS_MAKEORDER => array(
				'name' => array(
					'en' => 'Yandex Status: Make order',
					'ru' => 'Яндекс Статус: Заказ создан'
				),
				'color' => '#000028',
				'id' => self::STATUS_MAKEORDER,
				'paid' => false,
				'shipped' => false,
				'logable' => false,
				'delivery' => false
			),
			self::STATUS_UNPAID => array(
				'name' => array(
					'en' => 'Yandex Status: Unpaid',
					'ru' => 'Яндекс Статус: Не оплачен'
				),
				'color' => '#ff1c30',
				'id' => self::STATUS_UNPAID,
				'paid' => false,
				'shipped' => false,
				'logable' => false,
				'delivery' => false
			),
			self::STATUS_RESERVATION_EXPIRED => array(
				'name' => array(
					'en' => 'Yandex Status: Reservation expired',
					'ru' => 'Яндекс Статус: Резерв просрочен'
				),
				'color' => '#ff2110',
				'id' => self::STATUS_RESERVATION_EXPIRED,
				'paid' => false,
				'shipped' => false,
				'logable' => false,
				'delivery' => false
			),
			self::STATUS_RESERVATION => array(
				'name' => array(
					'en' => 'Yandex Status: Reservation',
					'ru' => 'Яндекс Статус: Резерв'
				),
				'color' => '#0f00d3',
				'id' => self::STATUS_RESERVATION,
				'paid' => false,
				'shipped' => false,
				'logable' => false,
				'delivery' => false
			),
		);
	}

	public function install()
	{
		$this->installOrderStates();
		$this->installSQL();
		return parent::install()
		&& $this->registerHook('actionOrderStatusUpdate')
		&& $this->registerHook('displayAdminOrder')
		&& $this->registerHook('displayBackOfficeHeader');
	}

	public function uninstall()
	{
		$this->uninstallOrderStates();
		$this->uninstallSQL();
		return parent::uninstall();
	}

	public function installOrderStates()
	{
		foreach ($this->order_states as $order_state)
		{
			$os = new OrderState((int)$order_state['id']);
			$os->force_id = true;
			$os->module_name = $this->name;

			foreach ($order_state as $key => $value)
			{
				if (property_exists($os, $key))
				{
					if (!is_array($value))
						$os->{$key} = $value;
					else
					{
						$os->{$key} = array();
						foreach (Language::getLanguages(false) as $l)
							$os->{$key}[(int)$l['id_lang']] = array_key_exists($l['iso_code'], $value) ? $value[$l['iso_code']] : $value['en'];
					}
				}
			}

			$os->add();
			if (version_compare(_PS_VERSION_, '1.6.0.9', '<'))
			{
				Db::getInstance()->update('order_state', array(
					'id_order_state' => (int)$order_state['id']
				), ' `id_order_state` = '.(int)$os->id);
				Db::getInstance()->update('order_state_lang', array(
					'id_order_state' => (int)$order_state['id']
				), ' `id_order_state` = '.(int)$os->id);
			}
		}
	}

	public function uninstallOrderStates()
	{
		foreach (array_keys($this->order_states) as $id_order_state)
		{
			$order_state = new OrderState($id_order_state);
			$order_state->delete();
		}
	}

	public function installSQL()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sya_order`
			(
				`id_sya_order` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_order` int(10) NOT NULL,
				`id_market_order` varchar(100) NOT NULL,
				`currency` varchar(100) NOT NULL,
				`payment_type` varchar(100) NOT NULL,
				`home` varchar(100) NOT NULL,
				`payment_method` varchar(100) NOT NULL,
				`outlet` varchar(100) NOT NULL,
				 PRIMARY KEY (`id_sya_order`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

		Db::getInstance()->execute($sql);
	}

	public function uninstallSQL()
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'sya_order');
	}

	protected function getDefaults()
	{
		return array(
			'OM_SHA1' => '',
			'CARD_ON_DELIVERY' => 1,
			'CASH_ON_DELIVERY' => 1,
			'OM_CAMPAIGN_ID' => '',
			'OM_TOKEN' => '',
			'OM_APP_ID' => '',
			'CARRIERS' => '',
			'ID_CUSTOMER' => '',
			'OM_API_URL' => 'https://api.partner.market.yandex.ru/v2/',
			'OM_ENABLE_CHANGE_DELIVERY' => 0
		);
	}

	public function getAngularValues()
	{
		return array(
			$this->getName().'_config' => $this->getConfiguration(),
			'carriers' => Carrier::getCarriers($this->context->language->id, false,
				false, false, null, Carrier::ALL_CARRIERS)
		);
	}

	public function getConfiguration()
	{
		return array(
			'OM_SHA1' => SYAConfigurationTools::get('OM_SHA1'),
			'CARD_ON_DELIVERY' => (int)SYAConfigurationTools::get('CARD_ON_DELIVERY'),
			'CASH_ON_DELIVERY' => (int)SYAConfigurationTools::get('CASH_ON_DELIVERY'),
			'OM_CAMPAIGN_ID' => SYAConfigurationTools::get('OM_CAMPAIGN_ID'),
			'OM_TOKEN' => SYAConfigurationTools::get('OM_TOKEN'),
			'OM_APP_ID' => SYAConfigurationTools::get('OM_APP_ID'),
			'CARRIERS' => (SYAConfigurationTools::get('CARRIERS') && SYATools::isJSON(SYAConfigurationTools::get('CARRIERS'))
				? Tools::jsonDecode(SYAConfigurationTools::get('CARRIERS'), true) : array()),
			'OM_API_URL' => SYAConfigurationTools::get('OM_API_URL'),
			'OM_ENABLE_CHANGE_DELIVERY' => (int)SYAConfigurationTools::get('OM_ENABLE_CHANGE_DELIVERY'),
			'img_url' => _MODULE_DIR_.$this->module->name.'/views/img/ordermarket/'
		);
	}

	public function getDefaultCustomer()
	{
		$id_customer = (int)SYAConfigurationTools::get('ID_CUSTOMER');
		$customer = new Customer($id_customer);
		if (!Validate::isLoadedObject($customer))
		{
			$customer->firstname = 'Order market not delete';
			$customer->lastname = 'Order market not delete';
			$customer->email = 'ordermarket@demo.demo';
			$customer->passwd = pSQL(Tools::encrypt('SEOSAYSPASS12345678'));
			$customer->newsletter = 1;
			$customer->optin = 1;
			$customer->active = 0;
			if ($customer->save())
				SYAConfigurationTools::update('ID_CUSTOMER', $customer->id);
		}

		return $customer;
	}

	/**
	 * @return bool
	 */
	public function isEnabledByDefault()
	{
		return true;
	}

	public function hookActionOrderStatusUpdate($params)
	{
		$new_os = $params['newOrderStatus'];
		if (array_key_exists($new_os->id, $this->order_states))
		{
			$yandex_order = SYAOrder::getInstanceByIdOrder((int)$params['id_order']);
			$id_market_order = $yandex_order->id_market_order;
			if ($id_market_order)
			{
				$order = SYAToolsOM::getOrder($id_market_order);
				$status = $order['status'];
				if ($status == 'PROCESSING' && ($new_os->id == self::STATUS_DELIVERY || $new_os->id == self::STATUS_CANCELLED))
					SYAToolsOM::sendOrder($this->getStatusNameById($new_os->id), $id_market_order);
				elseif ($status == 'DELIVERY' && ($new_os->id == self::STATUS_DELIVERED
						|| $new_os->id == self::STATUS_PICKUP
						|| $new_os->id == self::STATUS_CANCELLED))
					SYAToolsOM::sendOrder($this->getStatusNameById($new_os->id), $id_market_order);
				elseif ($status == 'PICKUP' && ($new_os->id == self::STATUS_DELIVERED || $new_os->id == self::STATUS_CANCELLED))
					SYAToolsOM::sendOrder($this->getStatusNameById($new_os->id), $id_market_order);
				elseif ($status == 'RESERVATION_EXPIRED' || $status == 'RESERVATION')
					return false;
				else
					return false;
			}
		}
	}

	/**
	 * @return string
	 */
	public function hookDisplayBackOfficeHeader()
	{
		$this->module->addJS('admin/components/ordermarket/admin.js');

		if (Tools::isSubmit('updateCarrierOrderMarket'))
		{
			$errors = array();

			$id_order = (int)Tools::getValue('id_order');
			$new_carrier = Tools::getValue('new_carrier');
			$price_incl = (float)Tools::getValue('price_incl');
			$price_excl = (float)Tools::getValue('price_excl');
			$order = new Order($id_order);

			if (!$new_carrier)
				$errors[] = $this->l('Carrier not select!');
			else
			{
				if (!Validate::isLoadedObject($order))
					$errors[] = $this->l('Order not founded!');
				else
				{
					$total_carrier_wt = (float)$order->total_products_wt + (float)$price_incl;
					$total_carrier = (float)$order->total_products + (float)$price_excl;

					$order->total_paid = (float)$total_carrier_wt;
					$order->total_paid_tax_incl = (float)$total_carrier_wt;
					$order->total_paid_tax_excl = (float)$total_carrier;
					$order->total_paid_real = (float)$total_carrier_wt;
					$order->total_shipping = (float)$price_incl;
					$order->total_shipping_tax_excl = (float)$price_excl;
					$order->total_shipping_tax_incl = (float)$price_incl;
					$order->carrier_tax_rate = (float)$order->carrier_tax_rate;
					$order->id_carrier = (int)$new_carrier;
					if (!$order->update())
						$errors[] = $this->l('Can not update order!');
					else
					{
						if ($order->invoice_number > 0)
						{
							$order_invoice = new OrderInvoice($order->invoice_number);
							$order_invoice->total_paid_tax_incl = (float)$total_carrier_wt;
							$order_invoice->total_paid_tax_excl = (float)$total_carrier;
							$order_invoice->total_shipping_tax_excl = (float)$price_excl;
							$order_invoice->total_shipping_tax_incl = (float)$price_incl;
							if (!$order_invoice->update())
								$errors[] = $this->l('Can not update order invoice');
						}

						$id_order_carrier = Db::getInstance()->getValue('
							SELECT `id_order_carrier`
							FROM `'._DB_PREFIX_.'order_carrier`
							WHERE `id_order` = '.(int)$order->id);

						if ($id_order_carrier)
						{
							$order_carrier = new OrderCarrier($id_order_carrier);
							$order_carrier->id_carrier = $order->id_carrier;
							$order_carrier->shipping_cost_tax_excl = (float)$price_excl;
							$order_carrier->shipping_cost_tax_incl = (float)$price_incl;
							if (!$order_carrier->update())
								$errors[] = $this->l('Cannot update order carrier');
						}
					}
				}
			}

			if (!count($errors))
			{
				SYAToolsOM::sendDelivery($order);
				Tools::redirectAdmin($this->context->link->getAdminLink($this->context->controller->controller_name, true)
					.'&id_order='.(int)$order->id.'&vieworder');
			}
			else
				$this->context->controller->errors = $errors;
		}
	}

	public function ajaxProcessGetPrice()
	{
		$id_carrier = (int)Tools::getValue('id_carrier');
		$id_order = (int)Tools::getValue('id_order');

		$result = array(
			'hasError' => false,
			'errors' => array()
		);

		$order = new Order($id_order);
		$cart = New Cart($order->id_cart);
		$carrier_list = $cart->getDeliveryOptionList();
		if (isset($carrier_list[$order->id_address_delivery][$id_carrier.',']['carrier_list'][$id_carrier]))
		{
			$carrier = $carrier_list[$order->id_address_delivery][$id_carrier.',']['carrier_list'][$id_carrier];
			$pr_incl = $carrier['price_with_tax'];
			$pr_excl = $carrier['price_without_tax'];

			$result['price_without_tax'] = $pr_excl;
			$result['price_with_tax'] = $pr_incl;
		}
		else
		{
			$result['hasError'] = true;
			$result['errors'][] = $this->l('Wrong carrier');
		}

		die(Tools::jsonEncode($result));
	}

	public function hookDisplayAdminOrder($params)
	{
		$id_order = (int)$params['id_order'];
		$yandex_order = SYAOrder::getInstanceByIdOrder($id_order);
		//$config = $this->getConfiguration();
		//$config_carriers = $config['CARRIERS'];
		$om_enable_change_delivery = (int)SYAConfigurationTools::get('OM_ENABLE_CHANGE_DELIVERY');

		$statuses = array();
		if ($yandex_order->id_market_order)
		{
			$order = SYAToolsOM::getOrder($yandex_order->id_market_order);
			if ($order)
			{
				$state = $order['status'];
				$st = array('PROCESSING', 'DELIVERY', 'PICKUP');
				if (!in_array($state, $st))
					$om_enable_change_delivery = false;

				if ($state == $this->getStatusNameById(self::STATUS_PROCESSING))
					$statuses = array(self::STATUS_RESERVATION_EXPIRED,
						self::STATUS_PROCESSING, self::STATUS_DELIVERED,
						self::STATUS_PICKUP,
						self::STATUS_MAKEORDER,
						self::STATUS_UNPAID);
				elseif ($state == $this->getStatusNameById(self::STATUS_DELIVERY))
				{
					$statuses = array(self::STATUS_RESERVATION_EXPIRED,
						self::STATUS_RESERVATION,
						self::STATUS_PROCESSING,
						self::STATUS_DELIVERY,
						self::STATUS_MAKEORDER,
						self::STATUS_UNPAID);
					if (!isset($order['delivery']['outletId'])
					|| $order['delivery']['outletId'] < 1 || $order['delivery']['outletId'] == '')
						$statuses[] = self::STATUS_PICKUP;
				}
				elseif ($state == $this->getStatusNameById(self::STATUS_PICKUP))
				{
					$statuses = array(self::STATUS_RESERVATION_EXPIRED,
						self::STATUS_RESERVATION,
						self::STATUS_PROCESSING,
						self::STATUS_PICKUP,
						self::STATUS_DELIVERY,
						self::STATUS_MAKEORDER, self::STATUS_UNPAID);
				}
				else
				{
					$statuses = array(self::STATUS_RESERVATION_EXPIRED,
						self::STATUS_RESERVATION,
						self::STATUS_PROCESSING,
						self::STATUS_DELIVERED,
						self::STATUS_PICKUP,
						self::STATUS_CANCELLED,
						self::STATUS_DELIVERY,
						self::STATUS_MAKEORDER,
						self::STATUS_UNPAID);
				}

			}

			$order_states = OrderState::getOrderStates($this->context->language->id);
			foreach ($order_states as $os)
			{
				if ($os['id_order_state'] > 899) continue;
				$statuses[] = $os['id_order_state'];
			}
		}
		else
		{
			$statuses = array_keys($this->order_states);
			$om_enable_change_delivery = false;
		}

		$this->context->smarty->assign(array(
			'statuses' => $statuses,
			'OM_ENABLE_CHANGE_DELIVERY' => $om_enable_change_delivery
		));

		if ($om_enable_change_delivery)
		{
			$order_object = new Order($id_order);
			$cart = new Cart($order_object->id_cart);
			$carriers = $cart->simulateCarriersOutput();

			array_unshift($carriers, array(
				'id_carrier' => 0,
				'name' => $this->l('---- Please select carrier ----')
			));

//			foreach ($carriers as $key => $carrier)
//			{
//				$id_carrier = str_replace(',', '', Cart::desintifier($carrier['id_carrier']));
//				$type = array_key_exists($id_carrier, $config_carriers) ? $config_carriers[$id_carrier] : 'POST';
//			}

			$this->context->smarty->assign(array(
				'carriers' => $carriers
			));
		}

		return $this->render($this->getFrontTemplatePath('display_admin_order.tpl'));
	}

	public function getStatusNameById($id)
	{
		switch ($id)
		{
			case self::STATUS_DELIVERY:
				return 'DELIVERY';
			case self::STATUS_CANCELLED:
				return 'CANCELLED';
			case self::STATUS_PICKUP:
				return 'PICKUP';
			case self::STATUS_PROCESSING:
				return 'PROCESSING';
			case self::STATUS_DELIVERED:
				return 'DELIVERED';
			case self::STATUS_MAKEORDER:
				return 'MAKEORDER';
			case self::STATUS_UNPAID:
				return 'UNPAID';
			case self::STATUS_RESERVATION_EXPIRED:
				return 'RESERVATION_EXPIRED';
			case self::STATUS_RESERVATION:
				return 'RESERVATION';
		}
	}
}