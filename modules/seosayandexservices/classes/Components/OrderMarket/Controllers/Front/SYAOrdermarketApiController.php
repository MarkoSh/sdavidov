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
 * @author    SeoSA<885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class SYAOrdermarketApiController
 */
class SYAOrdermarketApiController extends SYAKassaComponentFrontController
{
	public function init()
	{
		$this->display_header = false;
		$this->display_footer = false;
		return parent::init();
	}

	/**
	 * @var SYAMarket
	 */
	public $component;

	/**
	 * @ void
	 */
	public function initContent()
	{
		$query = Tools::getValue('query');
		$data = explode('?', $query);
		$json = call_user_func('file_get_contents', 'php://input');
		$array_json = Tools::jsonDecode($json, true);

		header_remove();
		if (function_exists('http_response_code'))
			http_response_code(200);
		header('Cache-Control: no-transform,public,max-age=300,s-maxage=900');
		header('Content-Type: application/json');
		header('Status: 200 OK');

		$handler = 'handlerRequest'.Tools::toCamelCase(str_replace('/', '_', ltrim($data[0], '/')), true);
		if (method_exists($this, $handler))
		{
			try
			{
				$this->{$handler}($array_json);
			}
			catch (Exception $e)
			{
				throw new PrestaShopException($e->getMessage());
			}
		}
		else
			throw new PrestaShopException('Method not exists!');

		parent::initContent();
	}

	/**
	 * @param array $request
	 */
	public function handlerRequestCart($request)
	{
		$config = $this->component->getConfiguration();
		$cart = $request['cart'];
		$currency = $cart['currency'];
		$items = $cart['items'];

		$id_currency = $this->getIdCurrencyByIso($currency);
		$this->setCookieCurrency($id_currency);

		$response = array(
			'cart' => array(
				'deliveryOptions' => array(),
				'items' => array(),
				'paymentMethods' => array()
			)
		);

		$cart_object = $this->createCartFromData($cart, true);

		if (is_array($items) && count($items))
			foreach ($items as $item)
			{
				$id_offer = $item['offerId'];
				$product_data = explode('-', $id_offer);
				$id_product = array_key_exists(0, $product_data) ? (int)$product_data[0] : null;
				$id_product_attribute = array_key_exists(1, $product_data) ? (int)$product_data[1] : null;

				$product = new Product($id_product);
				$available_count = (int)StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);
				if (!$product->active || $available_count < (int)$item['count'])
					continue;

				$count = min($available_count, (int)$item['count']);
				if ($id_product_attribute)
				{
					$combination = new Combination($id_product_attribute);
					if ($count < $combination->minimal_quantity)
						continue;
				}
				else
					if ($count < $product->minimal_quantity)
						continue;

				$price = Product::getPriceStatic($product->id, null, $id_product_attribute);
				$result = $cart_object->updateQty((int)$item['count'], (int)$product->id, (int)$id_product_attribute);
				$price = Tools::ps_round($price, 2);

				if ($result)
				{
					$response['cart']['items'][] = array(
						'feedId' => $item['feedId'],
						'offerId' => $item['offerId'],
						'price' => $price,
						'count' => (int)$count,
						'delivery' => true
					);
					$cart_object->update();
				}
			}

		$default_type = 'POST';
		$carriers = $config['CARRIERS'];
		foreach ($cart_object->simulateCarriersOutput() as $carrier)
		{
			$id_carrier = str_replace(',', '', Cart::desintifier($carrier['id_carrier']));
			$id_reference = SYAToolsOM::getIdReferenceByCarrier($id_carrier);
			$type = array_key_exists($id_reference, $carriers) && $carriers[$id_reference] ? $carriers[$id_reference] : $default_type;
			$key = count($response['cart']['deliveryOptions']);
			$response['cart']['deliveryOptions'][$key] = array(
				'id' => (string)$id_carrier,
				'serviceName' => pSQL($carrier['name']),
				'type' => pSQL($type),
				'price' => (float)$carrier['price'],
				'dates' => array(
					'fromDate' => date('d-m-Y'),
					'toDate' => date('d-m-Y'),
				)
			);

			if ($type == 'PICKUP')
				$response['cart']['deliveryOptions'][$key]['outlets'] = SYAToolsOM::getOutlets();
		}

		if ((int)SYAConfigurationTools::get('CARD_ON_DELIVERY'))
			$response['cart']['paymentMethods'][] = 'CARD_ON_DELIVERY';
		if ((int)SYAConfigurationTools::get('CASH_ON_DELIVERY'))
			$response['cart']['paymentMethods'][] = 'CASH_ON_DELIVERY';

		$cart_object->delete();
		$this->context->cookie->logout();
		if (is_array($request['cart']) && array_key_exists('delivery', $request['cart']) && !is_null($this->address))
			$this->address->delete();

		if (function_exists('http_response_code'))
			http_response_code(200);
		header('HTTP/1.0 200 Ok');
		die(Tools::jsonEncode($response));
	}

	/**
	 * @param array $request
	 */
	public function handlerRequestOrderAccept($request)
	{
		$order = $request['order'];
		$currency = $order['currency'];
		$items = $order['items'];
		$id_currency = $this->getIdCurrencyByIso($currency);
		$this->setCookieCurrency($id_currency);

		$accepted = false;
		$id_order = null;
		if (count($items))
		{
			$cart = $this->createCartFromData($order, true);

			foreach ($items as $item)
			{
				$id_offer = $item['offerId'];
				$product_data = explode('-', $id_offer);
				$id_product = array_key_exists(0, $product_data) ? (int)$product_data[0] : null;
				$id_product_attribute = array_key_exists(1, $product_data) ? (int)$product_data[1] : null;

				$product = new Product($id_product);
				$available_count = (int)StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);
				if (!$product->active || $available_count < (int)$item['count'])
					continue;

				$count = min($available_count, (int)$item['count']);
				if ($id_product_attribute)
				{
					$combination = new Combination($id_product_attribute);
					if ($count < $combination->minimal_quantity)
						continue;
				}
				else
					if ($count < $product->minimal_quantity)
						continue;
				$result = $cart->updateQty((int)$item['count'], (int)$product->id, (int)$id_product_attribute);

				if ($result)
					$cart->update();
			}

			if (count($items) == count($cart->getProducts())
				&& isset($order['paymentMethod'])
				&& isset($order['paymentType']))
			{
				if ($order['delivery']['id'] > 0)
				{
					$delivery_option = array($this->address->id => $order['delivery']['id'].',');
					$cart->setDeliveryOption($delivery_option);
					$cart->update();
				}

				$payment = new SYAPaymentMethod();
				$payment->name = $order['paymentType'].'_'.$order['paymentMethod'];
				$payment->module = 'seosayandexservices';
				$total = $cart->getOrderTotal(true, Cart::BOTH);

				if (array_key_exists('buyer', $order))
				{
					$result = $payment->validateOrder((int)$cart->id, SYAOrderMarket::STATUS_MAKEORDER, $total, $payment->name, '',
						array(),
						null,
						false,
						($cart->secure_key ? $cart->secure_key : ($this->customer->secure_key ? $this->customer->secure_key : false)));
				}
				else
				{
					$result = $payment->validateOrder((int)$cart->id, SYAOrderMarket::STATUS_RESERVATION, $total, $payment->name, '',
						array(),
						null,
						false,
						($cart->secure_key ? $cart->secure_key : ($this->customer->secure_key ? $this->customer->secure_key : false)));
				}

				if ($result)
				{
					$address = $order['delivery']['address'];

					$yandex_order = new SYAOrder();
					$yandex_order->id_order = (int)$payment->currentOrder;
					$yandex_order->id_market_order = pSQL($order['id']);
					$yandex_order->currency = pSQL($order['currency']);
					$yandex_order->payment_type = pSQL($order['paymentType']);
					$yandex_order->payment_method = pSQL($order['paymentMethod']);
					$yandex_order->home = (array_key_exists('home', $address) ? pSQL($address['home']) : '');
					$yandex_order->outlet = (isset($order['delivery']['outlet']) ? pSQL($order['delivery']['outlet']['id']) : '');
					$yandex_order->save();
					$accepted = true;
					$id_order = (int)$payment->currentOrder;
				}
			}
		}
		else
			$accepted = false;

		$response = array(
			'order' => array(
				'accepted' => (bool)$accepted
			)
		);

		if ($accepted)
			$response['order']['id'] = (string)$id_order;
		else
			$response['order']['reason'] = 'OUT_OF_DATE';

		if (function_exists('http_response_code'))
			http_response_code(200);
		header('HTTP/1.0 200 Ok');
		die(Tools::jsonEncode($response));
	}

	/**
	 * @param array $request
	 */
	public function handlerRequestOrderStatus($request)
	{
		$order = SYAOrder::getInstanceOrderByIdMarketOrder((int)$request['order']['id']);
		if (Validate::isLoadedObject($order) && $order->id_cart > 0)
		{
			$status = $request['order']['status'];
			if ($status == 'CANCELLED')
			{
				$sub = $request['order']['substatus'];
				if (isset($sub) && $sub == 'RESERVATION_EXPIRED')
					$order->setCurrentState((int)SYAOrderMarket::STATUS_RESERVATION_EXPIRED);
				else
					$order->setCurrentState((int)SYAOrderMarket::STATUS_CANCELLED);
			}

			if ($status == 'PROCESSING')
			{
				$order->setCurrentState((int)SYAOrderMarket::STATUS_PROCESSING);
				if (isset($request['order']['buyer']))
				{
					$buyer = $request['order']['buyer'];
					$customer = new Customer();
					$customer = $customer->getByEmail($buyer['email']);
					if (!Validate::isLoadedObject($customer))
					{
						$customer = new Customer();
						$customer->firstname = pSQL($buyer['firstName']);
						$customer->lastname = pSQL($buyer['lastName']);
						$customer->email = pSQL($buyer['email']);
						$customer->passwd = pSQL(Tools::encrypt('SEOSAYSPASS12345678'));
						$customer->newsletter = 1;
						$customer->optin = 1;
						$customer->active = 1;
						$customer->add();
					}

					$cart = new Cart($order->id_cart);
					$cart->id_customer = $customer->id;
					$cart->save();

					$address = new Address($cart->id_address_delivery);
					$address->firstname = pSQL($buyer['firstName']);
					$address->lastname = pSQL($buyer['lastName']);
					$address->phone_mobile = pSQL($buyer['phone']);
					$address->id_customer = $customer->id;
					$address->save();

					$order->id_customer = $customer->id;
					$order->save();
				}
			}
			if ($status == 'UNPAID')
				$order->setCurrentState((int)SYAOrderMarket::STATUS_UNPAID);

			if (function_exists('http_response_code'))
				http_response_code(200);
			header('HTTP/1.0 200 Ok');
			die('1');
		}
		die('2');
	}

	public function getIdCurrencyByIso($iso)
	{
		if ($iso == 'RUR')
			$id_currency = Currency::getIdByIsoCode('RUB');
		else
			$id_currency = Currency::getIdByIsoCode($iso);
		return $id_currency;
	}

	public function setCookieCurrency($id_currency)
	{
		$default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
		$this->context->cookie->id_currency = ($default_currency != $id_currency) ? $id_currency : $default_currency;
		$this->context->cookie->write();
		$this->context->currency = new Currency($this->context->cookie->id_currency);
	}

	/**
	 * @var Customer
	 */
	protected $customer = null;
	/**
	 * @var Address
	 */
	protected $address = null;

	/**
	 * @param array $data
	 * @param bool $add_user_data
	 * @return Cart
	 */
	public function createCartFromData($data, $add_user_data = false)
	{
		$cart = new Cart();
		if ($data['currency'] == 'RUR')
			$id_currency = Currency::getIdByIsoCode('RUB');
		else
			$id_currency = Currency::getIdByIsoCode($data['currency']);

		$default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
		$this->context->cookie->id_currency = ($default_currency != $id_currency ? $id_currency : $default_currency);
		$this->context->cookie->write();
		$this->context->currency = new Currency($this->context->cookie->id_currency);

		$cart->id_lang = (int)$this->context->language->id;
		$cart->id_currency = (int)$this->context->currency->id;
		$cart->id_guest = (int)$this->context->cookie->id_guest;
		$cart->add();
		$this->context->cookie->id_cart = (int)$cart->id;
		$this->context->cookie->write();

		if ($add_user_data)
		{
			$delivery = $data['delivery'];
			$address_data = array();

			if (isset($delivery['address']))
			{
				$address = $delivery['address'];
				$address_data['street'] = (array_key_exists('street', $address) ? $this->module->l('Street: ').$address['street'] : $this->module->l('Pickup'));
				$address_data['subway'] = (array_key_exists('subway', $address) ? $this->module->l('Metro: ').$address['subway'] : '');
				$address_data['block'] = (array_key_exists('block', $address) ? $this->module->l('Block: ').$address['block'] : '');
				$address_data['floor'] = (array_key_exists('floor', $address) ? $this->module->l('Floor: ').$address['floor'] : '');
				$address_data['house'] = (array_key_exists('house', $address)? $this->module->l('House: ').$address['house'] : '');
			}

			$address1 = implode(' ', $address_data);

			$phone = null;
			$firstname = null;
			$lastname = null;
			if (array_key_exists('buyer', $data))
			{
				$buyer = $data['buyer'];
				$customer = Customer::getByEmail($buyer['email']);

				$phone = $buyer['phone'];
				$firstname = pSQL($buyer['firstName']);
				$lastname = pSQL($buyer['lastName']);
				if (!Validate::isLoadedObject($customer))
				{
					$customer->firstname = $firstname;
					$customer->lastname = $lastname;
					$customer->email = pSQL($buyer['email']);
					$customer->passwd = pSQL(Tools::encrypt('SEOSAYSPASS12345678'));
					$customer->newsletter = 1;
					$customer->optin = 1;
					$customer->active = 1;
					$customer->add();
				}
			}
			else
				$customer = $this->component->getDefaultCustomer();

			if (is_null($firstname))
				$firstname = $customer->firstname;
			if (is_null($lastname))
				$lastname = $customer->lastname;

			$address_object = new Address();
			$address_object->firstname = pSQL($firstname);
			$address_object->lastname = pSQL($lastname);
			$address_object->phone_mobile = pSQL(!is_null($phone) ? $phone : '800000000');
			$address_object->phone = pSQL(!is_null($phone) ? $phone : '800000000');
			$address_object->postcode = (isset($address) && array_key_exists('postcode', $address) && Validate::isPostCode($address['postcode'])
				? $address['postcode'] : str_replace('N', '0', SYATools::getZipCodeFormat()));
			$address_object->address1 = ($address1 ? pSQL($address1) : $this->module->l('Empty'));

			$address_object->city = (isset($address) && array_key_exists('city', $address) ? $address['city'] : '');
			if (!$address_object->city)
				$address_object->city = (isset($delivery['region'])
				&& $delivery['region']['type'] == 'CITY' ? $delivery['region']['name'] : $this->module->l('City'));

			$address_object->alias = 'order_market_'.$cart->id;
			$address_object->id_customer = (int)$customer->id;
			$address_object->id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
			if ($address_object->save())
			{
				$cart->id_address_delivery = (int)$address_object->id;
				$cart->id_address_invoice = (int)$address_object->id;
				$cart->update();
				$this->address = $address_object;
			}
			$cart->id_customer = (int)$customer->id;
			$this->context->cookie->id_customer = (int)$customer->id;
			$this->context->cookie->write();
			$this->customer = $customer;
		}

		CartRule::autoRemoveFromCart($this->context);
		CartRule::autoAddToCart($this->context);

		return $cart;
	}
}