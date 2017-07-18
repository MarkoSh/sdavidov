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
 * Class SYAKassaComponentFrontController
 */
class SYAKassaComponentFrontController extends SYAComponentFrontController
{
	/**
	 * @var SYAAction
	 */
	protected $ya_action;
	public $ssl = true;
	public function init()
	{
		$this->ya_action = new SYAAction(Tools::getValue('shopId'),
			Tools::getValue('md5', md5('Demo')),
			Tools::getValue('orderNumber'),
			Tools::getValue('invoiceId', 0),
			Tools::getValue('orderSumAmount', 0),
			Tools::getValue('orderSumCurrencyPaycash', 0),
			Tools::getValue('orderSumBankPaycash', 0),
			Tools::getValue('customerNumber', 0));
		return parent::init();
	}
}