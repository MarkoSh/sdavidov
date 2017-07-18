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
 * Class SYAAction
 */
class SYAAction
{
	/*
		 * 0 - success
		 * 1 - auth error
		 * 100 - denied order
		 * 200 - error request
		 * */
	public $code = 0;
	public $error = array();

	public $shop_id;
	public $invoice_id = 0;
	public $order_sum_amount = 0.0;
	public $order_sum_currency_paycash = 0;
	public $order_sum_bank_paycash = 0;
	public $customer_number = 0;
	public $md5;
	public $order_number;

	public function __construct($shop_id, $md5, $order_number, $invoice_id = 0,
								$order_sum_amount = 0.00,
								$order_sum_currency_paycash = 0,
								$order_sum_bank_paycash = 0,
								$customer_number = 0)
	{
		$this->shop_id = $shop_id;
		$this->invoice_id = $invoice_id;
		$this->order_sum_amount = $order_sum_amount;
		$this->order_sum_currency_paycash = $order_sum_currency_paycash;
		$this->order_sum_bank_paycash = $order_sum_bank_paycash;
		$this->customer_number = $customer_number;
		$this->md5 = $md5;
		$this->order_number = $order_number;
	}

	public function checkOrder($order_sum_amount)
	{
		$callback = array(
			'action' => 'checkOrder',
			'orderSumAmount' => $order_sum_amount
		);
		$this->log(var_export($callback, true));
		$this->prepareCallback($callback);
		$this->log(var_export($callback, true));
		$this->checkParams($callback);
		$this->writeCheckOrderResponseXML();
	}

	public function paymentAviso($order_sum_amount, $return_code = false)
	{
		$callback = array(
			'action' => 'paymentAviso',
			'orderSumAmount' => $order_sum_amount
		);
		$this->prepareCallback($callback);
		$this->checkParams($callback);
		if ($return_code)
			return ($this->code == 0);
		$this->writePaymentAvisoResponseXML();
	}

	public function checkParams($callback)
	{
		$this->code = 0;
		$this->error = array();

		// ->l('Shop ID wrong!')
		if ($this->shop_id != $callback['shop_id'])
			$this->error[] = Translate::getModuleTranslation('seosayandexservices', 'Shop ID wrong!', 'yakaction');
		// ->l('Total order wrong!')
		//if (round($this->order_sum_amount) < round($callback['order_sum_amount']))
		if (round($this->order_sum_amount) < round($callback['orderSumAmount']))
			$this->error[] = Translate::getModuleTranslation('seosayandexservices', 'Total order wrong!', 'yakaction');
		if (count($this->error))
			$this->code = 100;

		if (!$this->checkSign($callback))
		{
			$this->code = 1;
			// ->l('Order wrong!')
			$this->error[] = Translate::getModuleTranslation('seosayandexservices', 'Order wrong!', 'yakaction');
		}
	}

	public function checkSign($callback)
	{
		$string = $callback['action'].';'.$this->order_sum_amount.';'
			.$this->order_sum_currency_paycash.';'.$this->order_sum_bank_paycash.';'
			.$callback['shop_id'].';'.$this->invoice_id.';'.$this->customer_number.';'.$callback['shop_password'];
		$md5 = md5($string);
		if (SYAConfigurationTools::get('DEMO_MODE'))
		{
			//$this->log('Request - '.var_export($_REQUEST, true));
			$this->log('String - '.$string);
			$this->log('String yandex - '.implode(';', $callback));
			$this->log('Md5 string - '.$md5);
			$this->log('Md5 yandex - '.$this->md5);
		}
		return (strcasecmp($this->md5, $md5) == 0);
	}

	public $file_log = null;
	public function log($message)
	{
		if (is_null($this->file_log))
			$this->file_log = fopen(_PS_MODULE_DIR_.'seosayandexservices/log.txt', 'a+');
		$message = date('H:i:s d-m-Y').': '.$message.PHP_EOL;
		fwrite($this->file_log, $message);
	}

	public function prepareCallback(&$callback)
	{
		$callback['shop_id'] = SYAConfigurationTools::get('SHOP_ID');
		$callback['shop_password'] = SYAConfigurationTools::get('SHOP_PASSWORD');
	}

	public function writeCheckOrderResponseXML()
	{
		header('Content-type: text/xml; charset=utf-8');
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('checkOrderResponse');
		$xml->setIndent(true);

		$attrs = array(
			'performedDatetime' => date('c'),
			'code' => $this->code,
			'shopId' => $this->shop_id,
			'invoiceId' => $this->invoice_id,
			'message' => Tools::substr(implode(', ', $this->error), 0, 254)
		);
		foreach ($attrs as $attr => $value)
			$xml->writeAttribute($attr, $value);

		$xml->endElement();
		$xml->endDocument();
		die($xml->outputMemory());
	}

	public function writePaymentAvisoResponseXML()
	{
		header('Content-type: text/xml; charset=utf-8');
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('paymentAvisoResponse');
		$xml->setIndent(true);

		$attrs = array(
			'performedDatetime' => date('c'),
			'code' => $this->code,
			'shopId' => $this->shop_id,
			'invoiceId' => $this->invoice_id
		);
		foreach ($attrs as $attr => $value)
			$xml->writeAttribute($attr, $value);

		$xml->endElement();
		$xml->endDocument();
		die($xml->outputMemory());
	}
}