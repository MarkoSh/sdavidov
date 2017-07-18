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
class SYAMoneyExternalAuthController extends SYAMoneyPaymentController
{
	/**
	 * @throws PrestaShopException
	 */
	public function initContent()
	{
		SYAComponentFrontController::initContent();

		$cart = $this->context->cart;
		$this->context->smarty->assign(array(
				'type' => 'card',
				'payment_url' => $this->context->link->getModuleLink($this->module->name, 'front', array(
						'component' => $this->component->getName(),
						'component_controller' => 'payment',
						'type' => 'card',
				)),
				'products_count' => $cart->nbProducts(),
				'order_total' => $cart->getOrderTotal(true, Cart::BOTH),
				'errors' => $this->errors
		));

		$this->setTemplate('confirmation.tpl');
	}

	public function postProcess()
	{
		if ($this->context->cart->id_customer == 0
			|| $this->context->cart->id_address_delivery == 0
			|| $this->context->cart->id_address_invoice == 0
			|| !$this->module->active)
			Tools::redirect($this->context->link->getPageLink('order'));

		$customer = new Customer($this->context->cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect($this->context->link->getPageLink('order'));

		$request_id = $this->context->cookie->sya_request_id;

		if ($request_id)
		{
			$status = Tools::getValue('status');
			$reason = Tools::getValue('reason');

			if ($status == 'success')
				$this->validateOrder('card');
			else
				$this->errors[] = $this->component->humanizeRequestExternalAuthError($reason);
		}
		else
			$this->errors[] = $this->module->l('Ошибка платежа');
	}
}