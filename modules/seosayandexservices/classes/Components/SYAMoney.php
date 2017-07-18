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
 * Class SYAMoney
 */
class SYAMoney extends SYAComponent
{
	const SIMULATION = false;

	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displayHeader')
		&& $this->registerHook('displayOrderConfirmation')
		&& $this->registerHook('displayPayment')
		&& $this->registerHook('paymentOptions');
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
			'MONEY_APP_ID' => '',
			'MONEY_SECRET_KEY' => '',
			'MONEY_TARGET_WALLET' => '',
			'MONEY_ENABLE_WALLET' => false,
			'MONEY_ENABLE_CARD' => false,
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
				'app_id' => self::getAppID(),
				'secret_key' => self::getAppOAuth2Secret(),
				'target_wallet' => self::getAppAccount(),
				'enable_wallet' => self::isWalletEnabled(),
				'enable_card' => self::isCardEnabled(),
		);
	}

	public function hookDisplayHeader()
	{
		if ($this->context->controller instanceof ParentOrderController
		|| $this->context->controller instanceof SYAMoneyPaymentController
		|| $this->context->controller instanceof SYAMoneyRedirectController)
			$this->module->addCSS('front/components/'.$this->getName().'.css');
	}

	/**
	 * @return string
	 */
	public function hookDisplayPayment()
	{
		SeoSAYandexServices::registerSmartyFunctions();

		$this->context->smarty->assign('component', $this);

		$wallet_redirect_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
				'component' => $this->getName(),
				'component_controller' => 'redirect',
				'type' => 'wallet',
		), true);

		$card_redirect_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
				'component' => $this->getName(),
				'component_controller' => 'redirect',
				'type' => 'card',
		), true);

		if (SYAMoney::SIMULATION)
		{
			$wallet_sp_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
					'component' => $this->getName(),
					'component_controller' => 'redirect',
					'type' => 'wallet',
					'code' => 'virtual_code',
			), true);
			$card_sp_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
					'component' => $this->getName(),
					'component_controller' => 'redirect',
					'type' => 'card',
					'code' => 'virtual_code',
			), true);
		}
		else
			$wallet_sp_url = $card_sp_url = sprintf('%s/oauth/authorize', SYAMoneyApi::getSpMoneyURL());

		$this->context->smarty->assign(array(
			'syamoney_wallet_sp_url' => $wallet_sp_url,
			'syamoney_card_sp_url' => $card_sp_url,
			'syamoney_app_id' => SYAMoney::getAppID(),
			'syamoney_wallet_redirect_url' => $wallet_redirect_url,
			'syamoney_card_redirect_url' => $card_redirect_url,
			'syamoney_wallet_scope' => $this->createAuthorizeScope(),
			'syamoney_card_scope' => $this->createAuthorizeScope(),
		));

		return $this->render($this->getFrontTemplatePath('payment.tpl'));
	}

	public function hookDisplayOrderConfirmation($params)
	{
		if (!$this->module->active || !$this->isEnabled())
			return null;

		if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
		{
			/**
			 * @var Order $order
			 */
			$order = $params['objOrder'];
			/**
			 * @var Currency $currency
			 */
			$currency = $params['currencyObj'];
		}
		else
		{
			/**
			 * @var Order $order
			 */
			$order = $params['order'];
			$currency = new Currency($order->id_currency);
		}

		if (!$order instanceof Order)
			return;

		if (!$currency instanceof Currency)
			return;

		$this->smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($order->getTotalPaid(), $currency, false),
				'status' => 'ok',
				'id_order' => $order->id,
				'shop_name' => (version_compare(_PS_VERSION_, '1.7.0.0', '<') ? $this->context->shop->name : array($this->context->shop->name))
		));

		return $this->render($this->getFrontTemplatePath('order-confirmation.tpl'));
	}

	public function createAuthorizeScope()
	{
		$currency = Currency::getIdByIsoCode('RUB');
		if ($this->context->currency->iso_code != 'RUB')
			$limit = Tools::convertPrice($this->context->cart->getOrderTotal(Configuration::get('PS_TAX')), $currency, -1);
		else
			$limit = $this->context->cart->getOrderTotal(Configuration::get('PS_TAX'));

		$scope = array(
			'payment.to-account("'.SYAMoney::getAppAccount().'","account").limit(,'.$limit.')',
			'money-source("wallet","card")'
		);

		return implode(' ', $scope);
	}

	public function hookPaymentOptions()
	{
		SeoSAYandexServices::registerSmartyFunctions();
		$options = array();

		$this->context->smarty->assign('component', $this);

		$wallet_redirect_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
			'component' => $this->getName(),
			'component_controller' => 'redirect',
			'type' => 'wallet',
		), true);

		$card_redirect_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
			'component' => $this->getName(),
			'component_controller' => 'redirect',
			'type' => 'card',
		), true);

		if (SYAMoney::SIMULATION)
		{
			$wallet_sp_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
				'component' => $this->getName(),
				'component_controller' => 'redirect',
				'type' => 'wallet',
				'code' => 'virtual_code',
			), true);
			$card_sp_url = $this->context->link->getModuleLink($this->module->name, 'front', array(
				'component' => $this->getName(),
				'component_controller' => 'redirect',
				'type' => 'card',
				'code' => 'virtual_code',
			), true);
		}
		else
			$wallet_sp_url = $card_sp_url = sprintf('%s/oauth/authorize', SYAMoneyApi::getSpMoneyURL());

		$this->context->smarty->assign(array(
			'syamoney_wallet_sp_url' => $wallet_sp_url,
			'syamoney_card_sp_url' => $card_sp_url,
			'syamoney_app_id' => SYAMoney::getAppID(),
			'syamoney_wallet_redirect_url' => $wallet_redirect_url,
			'syamoney_card_redirect_url' => $card_redirect_url,
			'syamoney_wallet_scope' => $this->createAuthorizeScope(),
			'syamoney_card_scope' => $this->createAuthorizeScope(),
		));

		if (SYAMoney::isWalletEnabled())
		{
			$option = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$option->setLogo(_MODULE_DIR_.$this->module->name.'/views/img/yandex-money-logo.png');
			$option->setCallToActionText($this->module->l('Yandex.Money Wallet', 'syamoney'))
				->setAction('')
				->setForm($this->context->smarty->fetch(_PS_MODULE_DIR_
					.'seosayandexservices/views/templates/front/components/money/payment_wallet.tpl'));
			$options[] = $option;
		}

		if (SYAMoney::isCardEnabled())
		{
			$option = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$option->setLogo(_MODULE_DIR_.$this->module->name.'/views/img/yandex-money-logo.png');
			$option->setCallToActionText($this->module->l('Yandex.Money Bank Card', 'syamoney'))
				->setAction('')
				->setForm($this->context->smarty->fetch(_PS_MODULE_DIR_
					.'seosayandexservices/views/templates/front/components/money/payment_card.tpl'));
			$options[] = $option;
		}

		return $options;
	}

	public function humanizeRequestPaymentError($error)
	{
		$errors = array (
				'illegal_params' => $this->l('Mandatory payment parameters are missing or invalid values.'),
				'illegal_param_label' => $this->l('Invalid parameter value label.'),
				'illegal_param_to' => $this->l('Недопустимое значение параметра to.'),
				'illegal_param_amount' => $this->l('Invalid parameter value to.'),
				'illegal_param_amount_due' => $this->l('Invalid parameter value amount due.'),
				'illegal_param_comment' => $this->l('Invalid parameter value comment.'),
				'illegal_param_message' => $this->l('Invalid parameter value message.'),
				'illegal_param_expire_period' => $this->l('Invalid parameter value expiry period.'),
				'not_enough_funds' => $this->l('On the payer\'s account is not enough money. It is necessary to recharge and spend a new payment.'),
				'payment_refused' => $this->l('The shop refused to accept payment
				(for example, the user tried to pay for the goods, which is not in the store).'),
				'payee_not_found' => $this->l('The recipient of the transfer could not be found.
				This account does not exist or a phone number / email, is not associated with a user account or the payee.'),
				'authorization_reject' => $this->l('The payment authorization is refused.
				Possible causes: the transaction with the current settings is forbidden for the user;
				 the user does not accept the agreement on the use of service Yandex.Money'),
				'limit_exceeded' => $this->l('Exceeded one of the limits for operation:
				on the amount of the transaction for authorization token issued;
				amount of the transaction for a period of time for the issued token authorization;
				Yandex.Money limits for different types of operations.'),
				'account_blocked' => $this->l('The user account is locked.
				To unlock the user account you want to send to the address specified in the account_unblock_uri.'),
				'ext_action_required' => $this->l('Currently, this type of payment can not be performed.
				For the possibility of such payments the user must go to the page at ext_action_uri and follow the instructions on this page.
				This may be the following: enter the credentials to take the offer to perform other actions in accordance with instructions'),
				'все прочие значения' => $this->l('Technical error, repeat the call at a later stage.'),
		);

		return array_key_exists($error, $errors) ?
				$errors[$error] :
				$this->l('Техническая ошибка, повторите вызов операции позднее.');
	}

	public function humanizeProcessPaymentError($error)
	{
		$errors = array(
				'contract_not_found' => $this->l('None created (but not proven) to specify the payment request id.'),
				'not_enough_funds' => $this->l('Insufficient funds in the account of the payer.
				It is necessary to recharge and spend a new payment.'),
				'limit_exceeded' => $this->l('Exceeded one of the limits for operation:
				on the amount of the transaction for authorization token issued;
				amount of the transaction for a period of time for the issued token authorization;
				Yandex.Money limits for different types of operations.'),
				'money_source_not_available' => $this->l('Requested payment method (money source) is not available for this payment.'),
				'illegal_param_csc' => $this->l('Missing or an invalid value csc.'),
				'payment_refused' => $this->l('The payment is refused.
				Possible causes: the shop refused to accept payment (request checkOrder);
				Yandex can not transfer the user (for example, exceeded the limit balance of the purse of the recipient).'),
				'authorization_reject' => $this->l('The payment authorization is refused. Possible causes:
				an expired credit card; the issuing bank rejected the transaction on the map;
				limit is exceeded for the user; the transaction with the current settings is forbidden for the user;
				the user does not accept the agreement on the use of the service "Yandex.Money".'),
				'account_blocked' => $this->l('The user account is locked.
				To unlock the user account you want to send to the address specified in the account_unblock_uri.'),
				'illegal_param_ext_auth_success_uri' => $this->l('Missing or an invalid value ext_auth_success_uri.'),
				'illegal_param_ext_auth_fail_uri' => $this->l('Missing or an invalid value ext_auth_fail_uri.'),
		);

		return array_key_exists($error, $errors) ?
				$errors[$error] :
				$this->l('The payment authorization is refused. The application should conduct a new payment after a while.');
	}

	/**
	 * @param $error
	 * @return mixed
	 */
	public function humanizeRequestExternalPaymentError($error)
	{
		$errors = array (
			'illegal_param_to' => $this->l('Invalid parameter value to.'),
			'illegal_param_amount' => $this->l('Invalid parameter value amount.'),
			'illegal_param_amount_due' => $this->l('Invalid parameter value amount due.'),
			'illegal_param_message' => $this->l('Invalid parameter value message.'),
			'payee_not_found' => $this->l('The recipient is not found, the specified account does not exist.'),
			'payment_refused' => $this->l('The shop refused to accept payment (for example,
			the user tried to pay for the goods, which is not in the store).'),
			'illegal_params' => $this->l('Required parameters are missing payment are invalid values and logical contradictions.'),
		);

		return array_key_exists($error, $errors) ?
				$errors[$error] :
				$errors['illegal_params'];
	}

	/**
	 * @param $error
	 * @return mixed
	 */
	public function humanizeProcessExternalPaymentError($error)
	{
		$errors = array (
			'illegal_param_request_id' => $this->l('Incorrect value request id or no context with the specified request id'),
			'illegal_param_csc' => $this->l('Missing or an invalid value csc.'),
			'illegal_param_instance_id' => $this->l('Missing or an invalid value instance_id.'),
			'illegal_param_money_source_token' => $this->l('Missing or an invalid value money source_token,
			 revoked or expired token of its validity period.'),
			'payment_refused' => $this->l('The payment is refused. Possible causes: the shop refused to accept payment (request checkOrder);
			translation of user Yandex.Money impossible (for example, exceeded the limit balance of the purse of the recipient).'),
			'authorization_reject' => $this->l('The payment authorization is refused. Possible causes:
			 the issuing bank rejected the transaction on the card, the transaction is not allowed with the current settings for that user.'),
			'illegal_param_ext_auth_success_uri' => $this->l('Missing or an invalid value ext_auth_success_uri.'),
			'illegal_param_ext_auth_fail_uri' => $this->l('Missing or an invalid value ext_auth_fail_uri.'),
		);

		return array_key_exists($error, $errors) ?
				$errors[$error] :
				$this->l('Неизвестная ошибка');
	}

	/**
	 * @param $error
	 * @return mixed
	 */
	public function humanizeRequestExternalAuthError($error)
	{
		$errors = array (
			'no3ds' => $this->l('Ошибка 3D Secure'),
		);

		return array_key_exists($error, $errors) ?
				$errors[$error] :
				$this->l('Ошибка платежа');
	}


	/**
	 * @return array
	 */
	public function getAngularValues()
	{
		return array(
			'money_config' => $this->getConfiguration(),
			'money_redirect_url' => $this->context->link->getModuleLink($this->module->name, 'front', array(), true),
		);
	}

	/**
	 * @return string
	 */
	public static function getAppID()
	{
		return (string)SYAConfigurationTools::get('MONEY_APP_ID');
	}

	/**
	 * @return string
	 */
	public static function getAppOAuth2Secret()
	{
		return (string)SYAConfigurationTools::get('MONEY_SECRET_KEY');
	}

	/**
	 * @return string
	 */
	public static function getAppAccount()
	{
		return (string)SYAConfigurationTools::get('MONEY_TARGET_WALLET');
	}

	/**
	 * @return bool
	 */
	public static function isWalletEnabled()
	{
		return (bool)SYAConfigurationTools::get('MONEY_ENABLE_WALLET');
	}

	/**
	 * @return bool
	 */
	public static function isCardEnabled()
	{
		return (bool)SYAConfigurationTools::get('MONEY_ENABLE_CARD');
	}
}