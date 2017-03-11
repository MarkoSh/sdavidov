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

class SkrillPaymentReturnModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $skrillErrorMessage = Tools::getValue('skrillErrorMessage');
        if (!empty($skrillErrorMessage)) {
            $skrillErrorMessage = $this->module->getLocaleErrorMapping($skrillErrorMessage);
        }
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
        
        $this->context->smarty->assign(array(
            'shop_name' => $this->context->shop->name,
            'skrillErrorMessage' => $skrillErrorMessage
        ));
        $this->setTemplate('payment_return.tpl');
    }
}
