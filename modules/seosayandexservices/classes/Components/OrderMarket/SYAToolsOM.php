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
 * Class SYAToolsOM
 */
class SYAToolsOM
{
	public static function getIdReferenceByCarrier($id_carrier)
	{
		return (int)Db::getInstance()->getValue('SELECT id_reference FROM '._DB_PREFIX_.'carrier
		WHERE id_carrier = '.(int)$id_carrier);
	}

	public static function getOutlets()
	{
		$json = SYAHttpTools::get(SYAConfigurationTools::get('OM_API_URL').'/campaigns/'.SYAConfigurationTools::get('OM_CAMPAIGN_ID')
			.'/outlets.json'.self::getOAuthParams());
		$outlets = Tools::jsonDecode($json, true);
		return is_array($outlets) && count($outlets) && array_key_exists('outlets', $outlets)
			? $outlets['outlets'] : array();
	}

	public static function getOrder($id)
	{
		$json = SYAHttpTools::get(SYAConfigurationTools::get('OM_API_URL').'/campaigns/'.SYAConfigurationTools::get('OM_CAMPAIGN_ID')
			.'/orders/'.(int)$id.'.json'.self::getOAuthParams());
		$order = Tools::jsonDecode($json, true);
		return is_array($order) && count($order) && array_key_exists('order', $order)
			? $order['order'] : array();
	}

	public static function sendOrder($state, $id)
	{
		$params = array(
			'order' => array(
				'status' => $state,
			)
		);

		if ($state == 'CANCELLED')
			$params['order']['substatus'] = 'SHOP_FAILED';

		$json = SYAHttpTools::put(SYAConfigurationTools::get('OM_API_URL').'/campaigns/'
			.SYAConfigurationTools::get('OM_CAMPAIGN_ID').'/orders/'.$id.'/status.json'.self::getOAuthParams(), $params);
		$order = Tools::jsonDecode($json, true);
		if (is_array($order) && count($order)
		&& array_key_exists('order', $order))
			return $order['order'];
		throw new PrestaShopException($json);
	}

	public static function sendDelivery($order)
	{
		$yandex_order = SYAOrder::getInstanceByIdOrder($order->id);
		$order_from_yandex = self::getOrder($yandex_order->id_market_order);

		$address = new Address($order->id_address_delivery);
		$carrier = New Carrier($order->id_carrier, Context::getContext()->language->id);
		$country = new Country($address->id_country, Context::getContext()->language->id);
		$date_time_string = explode(' ', $order->delivery_date);
		$carriers = (SYAConfigurationTools::get('CARRIERS') && SYATools::isSerialized(SYAConfigurationTools::get('CARRIERS'))
			? unserialize(SYAConfigurationTools::get('CARRIERS')) : array());

		$type = array_key_exists($carrier->id, $carriers) ? $carriers[$carrier->id] : 'POST';
		$params = array(
			'delivery' => array(
				'id' => $carrier->id,
				'type' => $type,
				'serviceName' => $carrier->name.'('.$carrier->delay.')',
				'dates' => array(
					'fromDate' => $date_time_string[0] > 0 ? date('d-m-Y', strtotime($date_time_string[0])) : date('d-m-Y'),
				)
			)
		);

		if ($order_from_yandex['paymentType'] == 'POSTPAID')
			$params['delivery']['price'] = $order->total_shipping;

		if ($type == 'PICKUP')
			$params['delivery']['outletId'] = $yandex_order->outlet;
		else
			$params['delivery']['address'] = array(
				'country' => $country->name,
				'postcode' => $address->postcode,
				'city' => $address->city,
				'house' => ($yandex_order->home ? $yandex_order->home : '-'),
				'street' => Tools::substr($address->address1.' '.($address->address2 ? $address->address2 : ''), 0, 50),
				'recipient' => $address->firstname.' '.$address->lastname,
				'phone' => $address->phone_mobile ? $address->phone_mobile : $address->phone,
			);

		$json = SYAHttpTools::put(SYAConfigurationTools::get('OM_API_URL').'/campaigns/'.SYAConfigurationTools::get('OM_CAMPAIGN_ID')
			.'/orders/'.(int)$yandex_order->id_market_order.'/delivery.json'.self::getOAuthParams(), $params);
		$order = Tools::jsonDecode($json, true);
		if (is_array($order) && count($order)
			&& array_key_exists('order', $order))
			return $order['order'];
		throw new PrestaShopException($json);
	}

	public static function getOAuthParams()
	{
		return '?oauth_token='.SYAConfigurationTools::get('OM_TOKEN')
			.'&oauth_client_id='.SYAConfigurationTools::get('OM_APP_ID');
	}
}