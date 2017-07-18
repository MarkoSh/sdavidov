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
abstract class SYAMoneyApi
{
	const DEMO = false;

	/**
	 * @return string
	 */
	public static function getMoneyURL()
	{
		return self::DEMO ? 'https://demomoney.yandex.ru' : 'https://money.yandex.ru';
	}

	/**
	 * @return string
	 */
	public static function getSpMoneyURL()
	{
		return self::DEMO ? 'https://sp.demomoney.yandex.ru' : 'https://sp-money.yandex.ru';
	}

	/**
	 * @param $client_id
	 * @param $redirect_uri
	 * @param $scope
	 * @return string
	 */
	public static function buildAuthorizeUrl($client_id, $redirect_uri, $scope)
	{
		$params = sprintf(
			'client_id=%s&response_type=%s&redirect_uri=%s&scope=%s',
			$client_id,
			'code',
			urlencode($redirect_uri),
			is_array($scope) ? implode(' ', $scope) : $scope
		);

		return sprintf('%s/oauth/authorize?%s', self::getSpMoneyURL(), $params);
	}

	public static function requestToken($client_id, $code, $redirect_uri, $client_secret = null)
	{
		if (SYAMoney::SIMULATION)
			return array(
				'access_token' => 'virtual_token'
			);

		$data = array(
			'code' => $code,
			'client_id' => $client_id,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $redirect_uri,
		);

		if (null !== $client_secret)
			$data['client_secret'] = $client_secret;

		return Tools::jsonDecode(SYAHttpTools::post(sprintf('%s/oauth/token', SYAMoneyApi::getSpMoneyURL()),
			$data
		), true);
	}

	/**
	 * @param $token
	 * @param $to_wallet
	 * @param $amount_due
	 * @param $label
	 * @param $message
	 * @param null $comment
	 * @param string $pattern_id
	 * @return array
	 */
	public static function requestPayment($token, $to_wallet, $amount_due, $label, $message, $comment = null, $pattern_id = 'p2p')
	{
		if (SYAMoney::SIMULATION)
			return array(
					'status' => 'success',
					'request_id' => 'virtual_request_id'
			);

		if (null === $comment)
			$comment = $message;

		return Tools::jsonDecode(SYAHttpTools::post(
				sprintf('%s/api/request-payment', self::getMoneyURL()),
				array(
					'pattern_id' => $pattern_id,
					'to' => $to_wallet,
					'amount_due' => $amount_due,
					'comment' => trim($comment),
					'message' => trim($message),
					'label' => $label,
				),
				array(
					'Authorization' => sprintf('Bearer %s', $token),
				)
		), true);
	}

	/**
	 * @param $token
	 * @param $request_id
	 * @return array
	 */
	public static function processPayment($token, $request_id)
	{
		if (SYAMoney::SIMULATION)
			return array(
					'status' => 'success',
			);

		return Tools::jsonDecode(SYAHttpTools::post(
				sprintf('%s/api/process-payment', self::getMoneyURL()),
				array(
					'request_id' => $request_id
				),
				array(
					'Authorization' => sprintf('Bearer %s', $token),
				)
		), true);
	}
}