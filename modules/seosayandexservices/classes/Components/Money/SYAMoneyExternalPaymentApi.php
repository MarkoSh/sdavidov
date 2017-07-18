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
 * Created by IntelliJ IDEA.
 * User: andrew
 * Date: 20.08.15
 * Time: 14:42
 */
abstract class SYAMoneyExternalPaymentApi
{
	/**
	 * @param $client_id
	 * @return array
	 */
	public static function getInstanceId($client_id)
	{
		return Tools::jsonDecode(SYAHttpTools::post(
			sprintf('%s/api/instance-id', SYAMoneyApi::getMoneyURL()),
			array(
				'client_id' => $client_id,
			)
		), true);
	}

	public static function requestPayment($instance_id, $to_wallet, $amount_due, $label, $message, $comment = null, $pattern_id = 'p2p')
	{
		if (SYAMoney::SIMULATION)
			return array(
					'status' => 'success',
					'request_id' => 'virtual_request_id'
			);

		if (null === $comment)
			$comment = $message;

		return Tools::jsonDecode(SYAHttpTools::post(
				sprintf('%s/api/request-external-payment', SYAMoneyApi::getMoneyURL()),
				array(
					'pattern_id' => $pattern_id,
					'instance_id' => $instance_id,
					'to' => $to_wallet,
					'amount_due' => $amount_due,
					'comment' => trim($comment),
					'message' => trim($message),
					'label' => $label,
				)
		), true);
	}

	/**
	 * @param $instance_id
	 * @param $request_id
	 * @param $ext_auth_success_uri
	 * @param $ext_auth_fail_uri
	 * @return array
	 */
	public static function processPayment($instance_id, $request_id, $ext_auth_success_uri, $ext_auth_fail_uri)
	{
		if (SYAMoney::SIMULATION)
			return array(
				'status' => 'success',
			);

		return Tools::jsonDecode(SYAHttpTools::post(
			sprintf('%s/api/process-external-payment', SYAMoneyApi::getMoneyURL()),
			array(
					'instance_id' => $instance_id,
					'request_id' => $request_id,
					'ext_auth_success_uri' => $ext_auth_success_uri,
					'ext_auth_fail_uri' => $ext_auth_fail_uri,
			)
		), true);
	}
}