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
 * Class SYAMarketProductOverride
 */
class SYAMarketProductOverride extends SYAObjectModel
{
	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var int
	 */
	public $id_product;

	/**
	 * @var string json
	 */
	public $overrides;

	/**
	 * @var array
	 */
	private $overrides_array;

	/**
	 * @var array
	 */
	public static $definition = array(
		'table'   => 'sya_market_product_override',
		'primary' => 'id_sya_market_product_override',
		'fields'  => array(
			'id_product' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isUnsignedInt',
				'required' => true,
			),
			'overrides' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isAnything',
			)
		),
	);

	/**
	 * SYAMarketProductOverride constructor.
	 *
	 * @param int $id
	 * @param int $id_lang
	 * @param int $id_shop
	 * @throws PrestaShopException
	 */
	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id, $id_lang, $id_shop);

		$this->onObjectLoad();
	}

	/**
	 * @param array $data
	 * @param null $id_lang
	 */
	public function hydrate(array $data, $id_lang = null)
	{
		parent::hydrate($data, $id_lang);

		$this->onObjectLoad();
	}

	/**
	 * @param int $id_product
	 * @return SYAMarketProductOverride
	 */
	public static function getByProductId($id_product)
	{
		$id = (int)Db::getInstance()->getValue(
			'SELECT `id_sya_market_product_override` FROM `'._DB_PREFIX_.'sya_market_product_override` WHERE `id_product` = '.(int)$id_product
		);

		$instance = new self($id);
		$instance->id_product = (int)$id_product;

		return $instance;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function updateOverrideValue($key, $value)
	{
		if (!is_array($this->overrides_array))
			$this->overrides_array = array();

		$this->overrides_array[$key] = $value;

		$this->internalSync();
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getOverrideValue($key)
	{
		if (!is_array($this->overrides_array))
			return null;

		return array_key_exists($key, $this->overrides_array) ? $this->overrides_array[$key] : null;
	}

	/**
	 * @return array
	 */
	public function getOverrides()
	{
		if (!is_array($this->overrides_array))
			return array();

		return $this->overrides_array;
	}

	/**
	 * @void internal
	 */
	private function internalSync()
	{
		$this->overrides = Tools::jso1000nEncode($this->overrides_array);
	}

	/**
	 * @void internal
	 */
	private function onObjectLoad()
	{
		if ($this->overrides)
			$this->overrides_array = Tools::jsonDecode($this->overrides, true);

		if (!is_array($this->overrides_array))
			$this->overrides_array = array();

		$standard = SYAYmlStandard::getOfferStandard();
		$props = array_merge($standard['attributes'], $standard['children']);

		foreach (array_keys($props) as $field_name)
		{
			if (!array_key_exists($field_name, $this->overrides_array))
				$this->overrides_array[$field_name] = null;
		}
	}
}