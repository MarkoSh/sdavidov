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
 * Class SYAKassaSuccessPaymentController
 */
class SYAKassaSuccessPaymentController extends SYAComponentFrontController
{
	/**
	 * @var SYAMarket
	 */
	public $component;

	/**
	 * @ void
	 */
	public function initContent()
	{
		parent::initContent();
		$cart = new Cart(Tools::getValue('orderNumber'));
		if (!Validate::isLoadedObject($cart))
		{
			if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0)
				Tools::redirect(Context::getContext()->link->getPageLink('order'));
			else
				Tools::redirect(Context::getContext()->link->getPageLink('order-opc'));
		}

		$id_order = Order::getOrderByCartId($cart->id);
		if ($id_order)
			Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='
					.$this->context->customer->secure_key.'&id_cart='.(int)$cart->id.'&id_module='
					.(int)$this->module->id.'&id_order='.(int)$id_order);
		else
		{
			Context::getContext()->smarty->assign(array(
					'path' => $this->module->l('Please wait payment!')
			));
			$this->setTemplate('wait_payment.tpl');
		}
	}
}