<?php
/**
* 2015 Skrill
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
*  @author    Skrill <contact@skrill.com>
*  @copyright 2015 Skrill
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Skrill
*/

require_once(dirname(__FILE__).'/../../core/core.php');

class SkrillPaymentAbstractModuleFrontController extends ModuleFrontController
{
    protected $paymentMethod = '';
    protected $templateName = 'skrill_form.tpl';
    public $ssl = true;

    public function initContent()
    {
        $this->display_column_left = false;
        $this->process();
        if (!isset($this->context->cart)) {
            $this->context->cart = new Cart();
        }
        if (!$this->useMobileTheme()) {
            $this->context->smarty->assign(array(
                'HOOK_HEADER' => Hook::exec('displayHeader'),
                'HOOK_LEFT_COLUMN' => ($this->display_column_left ? Hook::exec('displayLeftColumn') : ''),
                'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array(
                    'cart' => $this->context->cart)) : ''),
            ));
        } else {
            $this->context->smarty->assign('HOOK_MOBILE_HEADER', Hook::exec('displayMobileHeader'));
        }

        $cart = $this->context->cart;
        $messageLog =
            'Skrill - start payment process, method : '. $this->paymentMethod .
            ' by customer id : ' . $cart->id_customer;
        PrestaShopLogger::addLog($messageLog, 1, null, 'Cart', $cart->id, true);
        $contextLink = $this->context->link;

        PrestaShopLogger::addLog('Skrill - get post parameters', 1, null, 'Cart', $cart->id, true);
        $postParameters = $this->getPostParameters();
        $messageLog = 'Skrill - post parameters : ' . print_r($postParameters, true);
        PrestaShopLogger::addLog($messageLog, 1, null, 'Cart', $cart->id, true);
        
        PrestaShopLogger::addLog('Skrill - request sid', 1, null, 'Cart', $cart->id, true);
        try {
            $sid = SkrillPaymentCore::getSid($postParameters);
        } catch (Exception $e) {
            PrestaShopLogger::addLog('Skrill - sid is not generated', 3, null, 'Cart', $cart->id, true);
            Tools::redirect($contextLink->getPageLink('order', true, null, array(
                'step' => '3', 'skrillerror' => 'ERROR_GENERAL_REDIRECT')));
        }
        PrestaShopLogger::addLog('Skrill - sid : ' . print_r($sid, true), 1, null, 'Cart', $cart->id, true);

        if (!$sid) {
            PrestaShopLogger::addLog('Skrill - sid is not generated', 3, null, 'Cart', $cart->id, true);
            Tools::redirect($contextLink->getPageLink('order', true, null, array(
                'step' => '3', 'skrillerror' => 'ERROR_GENERAL_REDIRECT')));
        }

        PrestaShopLogger::addLog('Skrill - get widget url', 1, null, 'Cart', $cart->id, true);
        $redirectUrl = SkrillPaymentCore::getSkrillRedirectUrl($sid);
        PrestaShopLogger::addLog('Skrill - widget url : ' . $redirectUrl, 1, null, 'Cart', $cart->id, true);

        if (Configuration::get('SKRILL_GENERAL_DISPLAY') != "IFRAME") {
            Tools::redirect($redirectUrl);
        }
        $this->context->smarty->assign(array(
            'fullname' => $this->context->customer->firstname ." ". $this->context->customer->lastname,
            'lang'    => $this->getLang(),
            'redirectUrl' => $redirectUrl,
            'total' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
            'this_path' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
        ));
        $this->setTemplate($this->templateName);
    }

    private function getPostParameters()
    {
        $cart = $this->context->cart;
        $contextLink = $this->context->link;
        $customer = new Customer($cart->id_customer);
        $address = new Address((int)$cart->id_address_delivery);
        $country = new Country($address->id_country);
        $currency = new Currency((int)$cart->id_currency);
        $secureKey = $customer->secure_key;
        
        $dateTime = SkrillPaymentCore::getDateTime();
        $randomNumber = SkrillPaymentCore::randomNumber(4);
        $skrillSettings = $this->getSkrillSettings();

        if (empty($skrillSettings['merchant_id'])
            || empty($skrillSettings['merchant_account'])
            || empty($skrillSettings['recipient_desc'])
            || empty($skrillSettings['logo_url'])
            || empty($skrillSettings['api_passwd'])
            || empty($skrillSettings['secret_word'])) {
            $messageLog = 'Skrill - general setting is not completed. either of the parameter is not filled';
            PrestaShopLogger::addLog($messageLog, 3, null, 'Cart', $cart->id, true);
            Tools::redirect($contextLink->getPageLink('order', true, null, array(
                'step' => '3', 'skrillerror' => 'ERROR_GENERAL_REDIRECT')));
        }

        $postParameters = array();
        $postParameters['pay_to_email'] = $skrillSettings['merchant_account'];
        $postParameters['recipient_description'] = $skrillSettings['recipient_desc'];
        $postParameters['transaction_id'] = date('ymd').$cart->id.$dateTime.$randomNumber;
        $postParameters['return_url'] = $contextLink->getModuleLink(
            'skrill',
            'validation',
            array('cart_id' => $cart->id, 'payment_method' => $this->paymentMethod, 'secure_key' => $secureKey),
            true
        );
        $antiFraudHash = $this->module->generateAntiFraudHash(
            $this->context->cart->id,
            $this->paymentMethod,
            $cart->date_add
        );
        $postParameters['status_url'] = $this->context->link->getModuleLink(
            'skrill',
            'paymentStatus',
            array(
                'cart_id' => $this->context->cart->id,
                'payment_method' => $this->paymentMethod,
                'secure_payment' => $antiFraudHash
            ),
            true
        );
        $postParameters['status_url2'] = 'mailto:'.$skrillSettings['merchant_email'];
        $postParameters['cancel_url'] = $contextLink->getPageLink('order', true, null, array('step' => '3'));
        $postParameters['language'] = $this->getLang();
        $postParameters['logo_url'] = $skrillSettings['logo_url'];
        $postParameters['prepare_only'] = 1;
        $postParameters['pay_from_email'] = $this->context->customer->email;
        $postParameters['firstname'] = $this->context->customer->firstname;
        $postParameters['lastname'] = $this->context->customer->lastname;
        $postParameters['address'] = $address->address1;
        $postParameters['postal_code'] = $address->postcode;
        $postParameters['city'] = $address->city;
        $postParameters['country'] = SkrillPaymentCore::getCountryIso3($country->iso_code);
        $postParameters['amount'] = $cart->getOrderTotal(true, Cart::BOTH);
        $postParameters['currency'] = $currency->iso_code;
        $postParameters['detail1_description'] = "Order pay from ".$this->context->customer->email;
        $postParameters['merchant_fields'] = 'Platform,Developer';
        $postParameters['Platform'] = '21445510';
        $postParameters['Developer'] = 'Payreto';
        if ($this->paymentMethod != 'FLEXIBLE') {
            $postParameters['payment_methods'] = $this->paymentMethod;
        }

        return $postParameters;
    }
    
    private function getLang()
    {
        $language = new Language((int)$this->context->cart->id_lang);
        $languageCode = $language->iso_code;
 
        switch ($languageCode) {
            case 'de':
            case 'pl':
            case 'it':
            case 'fr':
            case 'es':
                return $languageCode;
        }

        return 'en';
    }

    private function getSkrillSettings()
    {
        $skrillSettings = array();
        $skrillSettings['merchant_id']      = Configuration::get('SKRILL_GENERAL_MERCHANTID');
        $skrillSettings['merchant_account'] = Configuration::get('SKRILL_GENERAL_MERCHANTACCOUNT');
        $skrillSettings['recipient_desc']   = Configuration::get('SKRILL_GENERAL_RECIPENT');
        $skrillSettings['logo_url']         = Configuration::get('SKRILL_GENERAL_LOGOURL');
        $skrillSettings['api_passwd']       = Configuration::get('SKRILL_GENERAL_APIPASS');
        $skrillSettings['secret_word']      = Configuration::get('SKRILL_GENERAL_SECRETWORD');
        $skrillSettings['merchant_email']   = Configuration::get('SKRILL_GENERAL_MERCHANTEMAIL');

        return $skrillSettings;
    }
}
