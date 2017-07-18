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
 * Class SYAOrder
 */
class SYAOrder extends ObjectModel
{
	/**
	 * @var int
	 */
	public $id_order;
	/**
	 * @var string
	 */
	public $id_market_order;
	/**
	 * @var string
	 */
	public $currency;
	/**
	 * @var string
	 */
	public $payment_type;
	/**
	 * @var string
	 */
	public $home;
	/**
	 * @var string
	 */
	public $payment_method;
	/**
	 * @var string
	 */
	public $outlet;


	public static $definition = array(
		'table' => 'sya_order',
		'primary' => 'id_sya_order',
		'fields' => array(
			'id_order' => array('type' => self::TYPE_INT, 'validation' => 'isUnsignedInt'),
			'id_market_order' => array('type' => self::TYPE_STRING, 'isString'),
			'currency' => array('type' => self::TYPE_STRING, 'isString'),
			'payment_type' => array('type' => self::TYPE_STRING, 'isString'),
			'home' => array('type' => self::TYPE_STRING, 'isString'),
			'payment_method' => array('type' => self::TYPE_STRING, 'isString'),
			'outlet' => array('type' => self::TYPE_STRING, 'isString')
		)
	);

	/**
	 * @param string $id_market_order
	 * @return Order
	 */
	public static function getInstanceOrderByIdMarketOrder($id_market_order)
	{
		$id_order = Db::getInstance()->getValue('SELECT id_order FROM '._DB_PREFIX_.bqSQL(self::$definition['table'])
			.' WHERE id_market_order = "'.pSQL($id_market_order).'"');
		return new Order($id_order);
	}

	/**
	 * @param int $id_order
	 * @return SYAOrder
	 */
	public static function getInstanceByIdOrder($id_order)
	{
		$id = Db::getInstance()->getValue('SELECT '.bqSQL(self::$definition['primary']).' FROM '._DB_PREFIX_.bqSQL(self::$definition['table'])
			.' WHERE id_order = '.(int)$id_order);
		return new self($id);
	}
}