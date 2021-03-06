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
 * Class SYAKassaPaymentAvisoController
 */
class SYAKassaPaymentAvisoController extends SYAKassaComponentFrontController
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
		$number_order = (int)Tools::getValue('orderNumber');
		$cart = new Cart($number_order);
		if (!Validate::isLoadedObject($cart))
			die($this->module->l('Cart can not loaded!'));
		$total = $cart->getOrderTotal(true);
		$total_rub = SYAConverter::convertToRUB($total, $cart->id_currency);

		if ($this->ya_action->paymentAviso($total_rub, true))
			$this->module->validateOrder((int)$cart->id, Configuration::get('PS_OS_PAYMENT'),
					$cart->getOrderTotal(true, Cart::BOTH), $this->module->displayName, null, array(), null, false, $cart->secure_key);

		$this->ya_action->paymentAviso($total_rub);
		parent::initContent();
	}
}