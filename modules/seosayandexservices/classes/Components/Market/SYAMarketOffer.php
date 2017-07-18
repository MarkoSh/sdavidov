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

class SYAMarketOffer
{
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $id_product;

	/**
	 * @var int
	 */
	protected $id_product_attribute;

	/**
	 * @var Product
	 */
	protected $product;
	/**
	 * @var Combination
	 */
	protected $combination;

	/**
	 * @var SYAMarketProductOverride
	 */
	protected $override;

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var array
	 */
	protected $features;

	/**
	 * @var array
	 */
	protected $attributes;

	/**
	 * SYAMarketOffer constructor.
	 * @param $id_product
	 * @param $id_product_attribute
	 */
	public function __construct($id_product, $id_product_attribute = null)
	{
		$this->context = Context::getContext();

		$this->id = (string)$id_product;
		$this->id_product = (int)$id_product;
		$this->product = new Product($this->id_product, true, $this->context->language->id);
		$this->override = SYAMarketProductOverride::getByProductId($id_product);

		if ($id_product_attribute)
		{
			$this->id .= 'pa'.(string)$id_product_attribute;
			$this->id_product_attribute = (int)$id_product_attribute;
			$this->combination = new Combination($this->id_product_attribute);
		}
	}

	/**
	 * @param $name
	 * @return mixed|string
	 */
	public function __get($name)
	{
		$override = $this->override->getOverrideValue($name);
		if ($override !== null)
			return $override;
		elseif ($this && property_exists($this, $name))
			return $this->{$name};
		elseif ($this->combination && property_exists($this->combination, $name))
			return $this->combination->{$name};
		elseif ($this->product && property_exists($this->product, $name))
			return $this->product->{$name};

		return null;
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return string
	 */
	public function __call($name, $arguments)
	{
		if ($this->override && method_exists($this->override, $name))
			return call_user_func_array(array($this->override, $name), $arguments);
		if ($this->combination && method_exists($this->combination, $name))
			return call_user_func_array(array($this->combination, $name), $arguments);
		elseif ($this->product && method_exists($this->product, $name))
			return call_user_func_array(array($this->product, $name), $arguments);

		throw new LogicException(sprintf('Undefined method: %s::%s', __CLASS__, $name));
	}

	/**
	 * @return float
	 */
	public function getPrice()
	{
		return Tools::ps_round($this->product->getPrice(true, $this->id_product_attribute));
	}

	/**
	 * @return float
	 */
	public function getOldPrice()
	{
		return Tools::ps_round(
			$this->product->getPrice(true, $this->id_product_attribute, 6, null, false, false)
		);
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getOverrideValue($key)
	{
		return $this->override->getOverrideValue($key);
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		$behaviour = SYAConfigurationTools::get('MARKET_DESCRIPTION_DEFAULT_BEHAVIOUR');
		$description = '';

		switch ($behaviour)
		{
			case 'features':
				$features = $this->doGetFeatures();
				if ($features)
				{
					foreach ($features as $feature)
						$description .= $feature['name'].': '.$feature['value'].', '.PHP_EOL;

					$description = rtrim(trim($description), ', '.PHP_EOL);
				}
				break;
			case 'description_short':
				$description = $this->description_short;
				break;
			case 'description':
			default:
				$description = $this->description;
				break;
		}

		return strip_tags($description);
	}

	/**
	 * @return array
	 */
	public function doGetFeatures()
	{
		if (!Feature::isFeatureActive())
			return array();

		$id_lang = (int)Context::getContext()->language->id;

		if (null === $this->features)
		{
			$sql = array(
				'SELECT fl.id_feature, IF(mf.`name` <> "", mf.`name`, fl.name) as name, fvl.value as value, mf.`unit`',
				'FROM `'._DB_PREFIX_.'feature_product` fp',
				'LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl',
				'ON fp.id_feature = fl.id_feature',
				'AND fl.id_lang = '.(int)$id_lang,
				'LEFT JOIN '._DB_PREFIX_.'market_feature mf ON mf.`id_feature` = fp.`id_feature`',
				'LEFT JOIN `'._DB_PREFIX_.'feature_value` fv ON (fp.id_feature_value = fv.id_feature_value)',
				'LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl',
				'ON fvl.id_feature_value = fv.id_feature_value',
				'AND fvl.id_lang = '.(int)$id_lang,
				'WHERE `id_product` = '.(int)$this->id_product,
			);

			$sql = implode(PHP_EOL, $sql);
			$features = array();
			foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $row)
				$features[(int)$row['id_feature']] = $row;

			$this->features = $features;
		}

		return $this->features;
	}

	public function doGetAttributes()
	{
		if (!Combination::isFeatureActive())
			return array();

		$id_lang = (int)Context::getContext()->language->id;

		if (null === $this->attributes)
		{
			$sql = new DbQuery();
			$sql->select('a.`id_attribute`');
			$sql->select('al.`name` as `value`');
			$sql->select('mag.`unit`');
			$sql->select('IF(mag.`name` <> "", mag.`name`, agl.name) as `name`');
			$sql->from('product_attribute_combination', 'pac');
			$sql->leftJoin('attribute', 'a', 'a.`id_attribute` = pac.`id_attribute`');
			$sql->leftJoin('attribute_lang', 'al', 'al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = '.(int)$id_lang);
			$sql->leftJoin('attribute_group_lang', 'agl', 'agl.`id_attribute_group` = a.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang);
			$sql->leftJoin('market_attribute_group', 'mag', 'mag.`id_attribute_group` = a.`id_attribute_group`');
			$sql->where('pac.`id_product_attribute` = '.(int)$this->combination->id);

			$attributes = array();
			foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $row)
				$attributes[(int)$row['id_attribute']] = $row;

			$this->attributes = $attributes;
		}
		return $this->attributes;
	}

	/**
	 * @return array
	 */
	public function getFeaturesAndAttributes()
	{
		$params = array();

		if (SYAConfigurationTools::get('MARKET_EXPORT_FEATURES'))
			$params = array_merge($params, $this->doGetFeatures());
		if (SYAConfigurationTools::get('MARKET_EXPORT_ATTRIBUTES_IN_PARAMS')
		&& SYAConfigurationTools::get('MARKET_EXPORT_COMBINATIONS')
		&& Validate::isLoadedObject($this->combination))
			$params = array_merge($params, $this->doGetAttributes());

		return $params;
	}

	/**
	 * @return array
	 */
	public function getOutlets()
	{
		$outlets_key_category = SYAMarket::getOutletsKeyCategory();
		if (isset($outlets_key_category[$this->product->id_category_default]))
			$outlets = $outlets_key_category[$this->product->id_category_default];
		else
			$outlets = array();

		$product_outlets = SYAMarket::getProductOutlets($this->product->id);
		if (is_array($product_outlets) && count($product_outlets))
			$outlets = $product_outlets;

		foreach ($outlets as &$outlet)
		{
			if ($outlet['booking'])
				$outlet['booking'] = 'true';
		}

		return $outlets;
	}

	/**
	 * @return null|string
	 */
	public function getPicture()
	{
		$cover = Product::getCover($this->product->id);
		if ($cover)
			return Context::getContext()->link->getImageLink(
				$this->product->link_rewrite,
				$this->product->id.'-'.$cover['id_image'],
				''
			);

		return null;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return Context::getContext()->link->getProductLink(
			$this->product,
			$this->product->link_rewrite,
			$this->product->category,
			null,
			Context::getContext()->language->id,
			Context::getContext()->shop->id,
			$this->id_product_attribute
		);
	}

	/**
	 * @return null
	 */
	public function getVendor()
	{
		if ($this->product->id_manufacturer)
		{
			$name = Manufacturer::getNameById($this->product->id_manufacturer);
			return $name ? $name : null;
		}

		return null;
	}

	/**
	 * @return null
	 */
	public function getVendorCode()
	{
		return $this->getCode('vendorCode');
	}

	/**
	 * @return null
	 */
	public function getBarcode()
	{
		return $this->getCode('barcode');
	}

	/**
	 * @param $type
	 * @return mixed|null|string
	 */
	protected function getCode($type)
	{
		$behaviour = SYAConfigurationTools::get('MARKET_'.Tools::strtoupper($type).'_DEFAULT_BEHAVIOUR');

		switch ($behaviour)
		{
			case 'ean13':
				return $this->ean13;
			case 'upc':
				return $this->upc;
			case 'reference':
				return $this->reference;
			default:
				return null;

		}
	}

	/**
	 * @return bool
	 */
	public function getAvailable()
	{
		$behaviour = SYAConfigurationTools::get('MARKET_AVAILABLE_DEFAULT_BEHAVIOUR');

		switch ($behaviour)
		{
			case 'all':
				return true;
			case 'none':
				return false;
			case 'by_quantity':
			default:
				return $this->getRealAvailable();
		}
	}

	/**
	 * @return bool
	 */
	public function getRealAvailable()
	{
		return (bool)StockAvailable::getQuantityAvailableByProduct(
			$this->id_product,
			$this->id_product_attribute
		);
	}

	public function getDeliveryOptions()
	{
		$delivery_options = $this->getOverrideValue('delivery-options');

		if (!is_array($delivery_options) && !count($delivery_options))
			return array();
		return $delivery_options;
	}
}