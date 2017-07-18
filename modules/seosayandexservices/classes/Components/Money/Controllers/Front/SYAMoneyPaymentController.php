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
 * Class SYAMoneyPaymentController
 */
class SYAMoneyPaymentController extends SYAMoneyFrontController
{
	/**
	 * @var SYAMoney
	 */
	public $component;

	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$method = 'request'.Tools::toCamelCase(Tools::getValue('type'), true).'Payment';
		$this->{$method}();
	}

	/**
	 * @param $type
	 */
	protected function successRedirect($type)
	{
		Tools::redirect($this->context->link->getPageLink(
			'order-confirmation',
			null,
			null,
			array(
				'id_cart' => $this->context->cart->id,
				'id_module' => $this->module->id,
				'id_order' => $this->module->currentOrder,
				'key' => $this->context->cart->secure_key,
				'type' => $type,
			)
		));
	}

	protected function validateOrder($type)
	{
		$this->module->validateOrder(
			(int)$this->context->cart->id,
			Configuration::get('PS_OS_PAYMENT'),
			$this->context->cart->getOrderTotal(true, Cart::BOTH),
			$this->module->l('Yandex.Деньги').' '.Tools::ucfirst($type),
			null,
			array(),
			null,
			false,
			$this->context->cart->secure_key
		);

		$this->successRedirect($type);
	}

	/**
	 * @throws PrestaShopException
	 */
	protected function requestWalletPayment()
	{
		do
		{
			$response = SYAMoneyApi::processPayment(
				$this->context->cookie->sya_encrypt_token,
				$this->context->cookie->sya_request_id
			);

			$status = $response && array_key_exists('status', $response) ? $response['status'] : null;

			if ($status === 'in_progress')
				sleep(1);

		}
		while ($status === 'in_progress');

		switch ($status)
		{
			case 'success':
				unset($this->context->cookie->sya_encrypt_token);
				unset($this->context->cookie->sya_request_id);
				$this->context->cookie->write();
				$this->validateOrder('wallet');
				break;
			default:
				$this->errors[] = $this->component->humanizeProcessPaymentError($response['error']);
				break;
		}

	}

	protected function requestCardPayment()
	{
		if (SYAMoney::SIMULATION)
		{
			$url = $this->context->link->getModuleLink($this->module->name, 'front', array(
				'component' => $this->component->getName(),
				'component_controller' => 'external_auth',
				'orderN' => 'virtual_order_n',
				'cps_card' => '000000******0000',
				'merchant_order_id' => 'virtual_merchant_order_id',
				'cps_context_id' => 'virtual_cps_context_id',
				'skr_env' => 'api',
				'status' => 'success',
				'sum' => $this->context->cart->getOrderTotal(Configuration::get('PS_TAX')),
			));

			Tools::redirect($url);
			return;
		}

		$ext_auth_uri = $this->context->link->getModuleLink($this->module->name, 'front', array(
			'component' => $this->component->getName(),
			'component_controller' => 'external_auth',
		));

		do
		{
			$response = SYAMoneyExternalPaymentApi::processPayment(
				$this->context->cookie->sya_instance_id,
				$this->context->cookie->sya_request_id,
				$ext_auth_uri,
				$ext_auth_uri
			);
			$status = $response && array_key_exists('status', $response) ? $response['status'] : null;

			if ($status === 'in_progress')
				sleep(1);

		} while ($status === 'in_progress');

		switch ($status)
		{
			case 'success':
				unset($this->context->cookie->sya_instance_id);
				unset($this->context->cookie->sya_request_id);
				$this->context->cookie->write();

				$this->validateOrder('card');
				break;
			case 'ext_auth_required':
				Tools::redirect(sprintf('%s?%s', $response['acs_uri'], http_build_query($response['acs_params'])), '');
				break;
			default:
				$this->errors[] = $this->component->humanizeProcessExternalPaymentError($response['error']);
				break;
		}
	}
}