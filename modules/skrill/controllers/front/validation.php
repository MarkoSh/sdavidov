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
require_once(dirname(__FILE__).'/../../core/versiontracker.php');

class SkrillValidationModuleFrontController extends ModuleFrontController
{
    protected $checkoutUrl = 'index.php?controller=order&step=1';
    protected $orderConfirmationUrl = 'index.php?controller=order-confirmation&id_cart=';
    protected $statusUrl = false;
    protected $refundType = 'fraud';

    public function postProcess()
    {
        sleep(3);
        $cartId = (int)Tools::getValue('cart_id');
        $orderId = Order::getOrderByCartId($cartId);

        $transactionId = Tools::getValue('transaction_id');
        $order = $this->module->getOrderByTransactionId($transactionId);

        if ($orderId) {
            $this->validateOrder($cartId, $order);
        } else {
            $this->redirectPaymentReturn();
        }
    }

    protected function validateOrder($cartId, $order)
    {
        $orderStatus = $order['order_status'];

        if ($orderStatus == $this->module->processedStatus || $orderStatus == $this->module->pendingStatus) {
            $this->redirectSuccess($cartId);
        } else {
            if ($orderStatus == $this->module->refundedStatus || $orderStatus == $this->module->refundFailedStatus) {
                $errorStatus = 'ERROR_GENERAL_FRAUD_DETECTION';
            } elseif ($orderStatus == $this->module->failedStatus) {
                $paymentResponse = $this->module->convertSerializeToArray($order['payment_response']);
                $errorStatus = SkrillPaymentCore::getSkrillErrorMapping($paymentResponse['failed_reason_code']);
            } else {
                $this->redirectPaymentReturn();
            }
            $this->redirectPaymentReturn($errorStatus);
        }
    }

    protected function redirectPaymentReturn($returnMessage = "")
    {
        Tools::redirect(
            $this->context->link->getModuleLink(
                'skrill',
                'paymentReturn',
                array(
                     'secure_key' => $this->context->customer->secure_key,
                     'skrillErrorMessage' => $returnMessage
                ),
                true
            )
        );
    }

    protected function redirectSuccess($cartId)
    {
        Tools::redirect(
            $this->orderConfirmationUrl.
            $cartId.
            '&id_module='.(int)$this->module->id.
            '&key='.$this->context->customer->secure_key
        );
    }
}
