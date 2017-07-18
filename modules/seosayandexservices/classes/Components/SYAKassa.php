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
 * Class SYAKassa
 */
class SYAKassa extends SYAComponent
{
	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displayPayment')
		&& $this->registerHook('displayPaymentReturn')
		&& $this->registerHook('paymentOptions');
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
			'SHOP_ID' => '',
			'SCID' => '',
			'SHOP_PASSWORD' => Tools::passwdGen(20),
			'DEMO_MODE' => 1,
			'payment_pc' => 1,
			'payment_ac' => 1,
			'payment_mc' => 1,
			'payment_gp' => 1,
			'payment_wm' => 1,
			'payment_sb' => 1,
			'payment_mp' => 1,
			'payment_ab' => 1,
			'payment_ma' => 1,
			'payment_pb' => 1,
			'payment_qw' => 1,
		);
	}


	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
			'shop_id' => SYAConfigurationTools::get('SHOP_ID'),
			'scid' => SYAConfigurationTools::get('SCID'),
			'shop_password' => SYAConfigurationTools::get('SHOP_PASSWORD'),
			'demo_mode' => (int)SYAConfigurationTools::get('DEMO_MODE'),
			'payment_pc' => (int)SYAConfigurationTools::get('PAYMENT_PC'),
			'payment_ac' => (int)SYAConfigurationTools::get('PAYMENT_AC'),
			'payment_mc' => (int)SYAConfigurationTools::get('PAYMENT_MC'),
			'payment_gp' => (int)SYAConfigurationTools::get('PAYMENT_GP'),
			'payment_wm' => (int)SYAConfigurationTools::get('PAYMENT_WM'),
			'payment_sb' => (int)SYAConfigurationTools::get('PAYMENT_SB'),
			'payment_mp' => (int)SYAConfigurationTools::get('PAYMENT_MP'),
			'payment_ab' => (int)SYAConfigurationTools::get('PAYMENT_AB'),
			'payment_ma' => (int)SYAConfigurationTools::get('PAYMENT_MA'),
			'payment_pb' => (int)SYAConfigurationTools::get('PAYMENT_PB'),
			'payment_qw' => (int)SYAConfigurationTools::get('PAYMENT_QW'),
		);
	}

	public function getTotalOrder()
	{
		$currency = Currency::getIdByIsoCode('RUB');
		if ($this->context->currency->iso_code != 'RUB')
			$total_order = Tools::convertPrice($this->context->cart->getOrderTotal(Configuration::get('PS_TAX')), $currency, -1);
		else
			$total_order = $this->context->cart->getOrderTotal(Configuration::get('PS_TAX'));
		return $total_order;
	}

	public function hookDisplayPayment()
	{
		$address = new Address($this->context->cart->id_address_delivery);

		$this->context->smarty->assign(array(
			'shop_id' => SYAConfigurationTools::get('SHOP_ID'),
			'scid' => SYAConfigurationTools::get('SCID'),
			'customer_number' => $this->context->customer->id,
			'customer_email' => $this->context->customer->email,
			'customer_phone' => ($address->phone_mobile ? $address->phone_mobile : $address->phone),
			'url' => $this->getURL(),
			'order_number' => $this->context->cart->id,
			'total_order' => $this->getTotalOrder(),
			'payment_methods' => $this->getPaymentMethods()
		));
		return $this->render($this->getFrontTemplatePath('payment.tpl'));
	}

	public function hookDisplayPaymentReturn()
	{
		return $this->render($this->getFrontTemplatePath('payment_return.tpl'));
	}

	public function hookPaymentOptions()
	{
		$options = array();
		$address = new Address($this->context->cart->id_address_delivery);
		$this->context->smarty->assign(array(
			'shop_id' => SYAConfigurationTools::get('SHOP_ID'),
			'scid' => SYAConfigurationTools::get('SCID'),
			'customer_number' => $this->context->customer->id,
			'customer_email' => $this->context->customer->email,
			'customer_phone' => ($address->phone_mobile ? $address->phone_mobile : $address->phone),
			'url' => $this->getURL(),
			'order_number' => $this->context->cart->id,
			'total_order' => $this->getTotalOrder()
		));

		foreach ($this->getPaymentMethods() as $payment_method)
		{
			if (!$payment_method['enabled'])
				continue;

			$this->context->smarty->assign('type', $payment_method['type']);
			$option = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$option->setLogo($payment_method['logo']);
			$option->setCallToActionText($payment_method['name'])
				->setAction($this->getURL())
				->setForm($this->context->smarty->fetch(_PS_MODULE_DIR_
					.'seosayandexservices/views/templates/front/components/kassa/payment_option.tpl'));
			$options[] = $option;
		}

		return $options;
	}

	public function getURL()
	{
		if (SYAConfigurationTools::get('DEMO_MODE'))
			return SYAConfig::DEMO_MONEY_URL;
		else
			return SYAConfig::MONEY_URL;
	}

	public function getPaymentMethods()
	{
		$logo_path = _MODULE_DIR_.'seosayandexservices/views/img/kassa/';
		return array(
			array(
				'type' => 'PC',
				'name' => $this->l('Payment from a purse in Yandex.Money'),
				'logo' => $logo_path.'pc.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_pc')
			),
			array(
				'type' => 'AC',
				'name' => $this->l('Payment of any credit card.'),
				'logo' => $logo_path.'ac.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_ac')
			),
			array(
				'type' => 'MC',
				'name' => $this->l('Payment by mobile phone account.'),
				'logo' => $logo_path.'mc.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_mc')
			),
			array(
				'type' => 'GP',
				'name' => $this->l('Cash and cash through the terminal.'),
				'logo' => $logo_path.'gp.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_gp')
			),
			array(
				'type' => 'WM',
				'name' => $this->l('Payment from a purse in system WebMoney.'),
				'logo' => $logo_path.'wm.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_wm')
			),
			array(
				'type' => 'SB',
				'name' => $this->l('Payment through the Savings Bank: payment by SMS or Online Savings.'),
				'logo' => $logo_path.'sb.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_sb')
			),
			array(
				'type' => 'MP',
				'name' => $this->l('Payment via mobile terminal (mPOS).'),
				'logo' => $logo_path.'mp.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_mp')
			),
			array(
				'type' => 'AB',
				'name' => $this->l('Payment by Alfa-Click.'),
				'logo' => $logo_path.'ab.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_ab')
			),
			array(
				'type' => 'MA',
				'name' => $this->l('Payment via MasterPass.'),
				'logo' => $logo_path.'ma.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_ma')
			),
			array(
				'type' => 'PB',
				'name' => $this->l('Payment by PromSviazBank.'),
				'logo' => $logo_path.'pb.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_pb')
			),
			array(
				'type' => 'QW',
				'name' => $this->l('Payment via QIWI Wallet.'),
				'logo' => $logo_path.'qw.png',
				'enabled' => (int)SYAConfigurationTools::get('payment_qw')
			)
		);
	}
}