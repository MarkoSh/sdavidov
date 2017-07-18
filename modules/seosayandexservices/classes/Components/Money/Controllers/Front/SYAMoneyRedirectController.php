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
 * Class SYAMoneyRedirectController
 */
class SYAMoneyRedirectController extends SYAMoneyFrontController
{
	/**
	 * @ void
	 */
	public function postProcess()
	{
		parent::postProcess();

		$code = Tools::getValue('code');

		if (!$code)
			Tools::redirect($this->context->link->getPageLink('order'));

		$method = 'request'.Tools::toCamelCase(Tools::getValue('type'), true).'Payment';
		$this->{$method}($code);
	}

	/**
	 * @param $code
	 */
	protected function requestWalletPayment($code)
	{
		$redirect_uri = $this->context->link->getModuleLink($this->module->name, 'front', array(
				'component' => $this->component->getName(),
				'component_controller' => 'redirect',
		));

		$response = SYAMoneyApi::requestToken(
				SYAMoney::getAppID(),
				$code,
				$redirect_uri,
				SYAMoney::getAppOAuth2Secret()
		);

		$token = array_key_exists('access_token', $response) ? $response['access_token'] : null;

		if ($token === null)
		{
			$url = SYAMoneyApi::buildAuthorizeUrl(
					SYAMoney::getAppID(),
					$redirect_uri,
					$this->component->createAuthorizeScope()
			);

			Tools::redirect($url, '');
			return;
		}

		$amount_due = $this->context->cart->getOrderTotal(Configuration::get('PS_TAX'));
		$message = $this->module->l('total:').$amount_due.$this->module->l(' rub');

		$response = SYAMoneyApi::requestPayment(
			$token,
			SYAMoney::getAppAccount(),
			$amount_due,
			$this->context->cart->id,
			$message
		);

		$status = $response && array_key_exists('status', $response) ? $response['status'] : null;

		switch ($status)
		{
			case 'success':
				$this->context->cookie->sya_encrypt_token = $token;
				$this->context->cookie->sya_request_id = $response['request_id'];
				$this->context->cookie->write();
				break;
			case 'refused':
				$this->errors[] = $this->component->humanizeRequestPaymentError($response['error']);
				break;
			case 'hold_for_pickup':
				$this->errors[] = $this->module->l('The recipient of the translation is not found, the translation will be sent on demand.');
				break;
			default:
				break;
		}
	}

	/**
	 * @void
	 */
	protected function requestCardPayment()
	{
		$amount_due = $this->context->cart->getOrderTotal(Configuration::get('PS_TAX'));
		$message = $this->module->l('total:').$amount_due.$this->module->l(' rub');

		$response = SYAMoneyExternalPaymentApi::getInstanceId(SYAMoney::getAppID());
		$status = $response && array_key_exists('status', $response) ? $response['status'] : null;

		if ($status === 'success')
		{
			$instance_id = $response['instance_id'];
			$response = SYAMoneyExternalPaymentApi::requestPayment(
				$instance_id,
				SYAMoney::getAppAccount(),
				$amount_due,
				$this->context->cart->id,
				$message
			);
			$status = $response && array_key_exists('status', $response) ? $response['status'] : null;
			switch ($status)
			{
				case 'success':
					$this->context->cookie->sya_instance_id = $instance_id;
					$this->context->cookie->sya_request_id = $response['request_id'];
					$this->context->cookie->write();
					break;
				case 'refused':
					$this->errors[] = $this->component->humanizeRequestExternalPaymentError($response['error']);
					break;
				case 'hold_for_pickup':
					$this->errors[] = $this->module->l('The recipient of the translation is not found, the translation will be sent on demand.');
					break;
				default:
					break;
			}
		}
	}
}