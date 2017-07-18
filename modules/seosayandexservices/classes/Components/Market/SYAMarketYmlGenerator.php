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
 * Class SYAMarketYmlGenerator
 */
class SYAMarketYmlGenerator
{
	/**
	 * @var SimpleXMLElement
	 */
	protected $xml;

	/**
	 * @var array
	 */
	protected $standard;

	/**
	 * @var SYAObjectFinder
	 */
	protected $finder;

	/**
	 * @var array
	 */
	protected static $cache = array();

	/**
	 * SYAMarketYmlGenerator constructor.
	 */
	public function __construct()
	{
		$this->finder = new SYAObjectFinder();
		$this->standard = SYAYmlStandard::getStandard();

		$this->xml = new SimpleXMLElement(
			'<?xml version="1.0" encoding="UTF-8"?>
			<!DOCTYPE yml_catalog SYSTEM "http://partner.market.yandex.ru/pages/help/shops.dtd">
			<yml_catalog></yml_catalog>'
		);
	}

	/**
	 * @return string
	 */
	public function generate()
	{
		$this->ymlNode($this->xml, $this->standard['yml_catalog']);

		return $this->exportXml();
	}

	/**
	 * @param SimpleXMLElement $node
	 * @param $standard
	 * @param $source
	 */
	protected function ymlNode(SimpleXMLElement $node, $standard, $source = null)
	{
		foreach ($standard as $key => $value)
		{
			switch ($key)
			{
				case 'attributes':
					$this->ymlAttrs($node, $value, $source);
					break;
				case 'children':
					foreach ($value as $name => $definition)
					{
						$child_node_name = array_key_exists('node_name', $definition) ? $definition['node_name'] : $name;

						/**
						 * @Huck replace tag_name from name to model, when type == vendor.model
						 */
						if (array_key_exists('attributes', $standard)
							&& array_key_exists('type', $standard['attributes'])
							&& $child_node_name == 'name')
						{
							$value = null;
							if (array_key_exists('value', $definition))
								$value = $this->getValueByDefinition('type', $standard['attributes']['type']['value'], $source);

							if ($value == 'vendor.model')
								$child_node_name = 'model';
						}

						if (array_key_exists('repeat', $definition) && $definition['repeat'])
						{
							if (is_string($definition['repeat']))
								$getter = $definition['repeat'];
							else
								$getter = 'get'.Tools::toCamelCase(str_replace('-', '_', $name), true).'s';

							$sources = $this->callGetter($getter, $source);
						}
						else
							$sources = array($source);

						foreach ($sources as $child_source)
						{
							$value = null;
							if (array_key_exists('value', $definition))
							{
								$required = array_key_exists('required', $definition) && $definition['required'];

								$value = $this->getValueByDefinition($name, $definition['value'], $child_source);
								if ($value);
									$value = htmlspecialchars($value);

								if ($required)
								{
									if ($value === false)
										throw new LogicException(sprintf('Required value "%s" is empty', $name));

									$this->ymlNode($node->addChild($child_node_name, $value), $definition, $child_source);
								}
								elseif ($value)
								{
									$include = true;
									if (array_key_exists('allow_exclude', $definition) && $definition['allow_exclude'])
									{
										$include = (bool)SYAConfigurationTools::get(
												'MARKET_'.Tools::strtoupper($name).'_INCLUDE_TO_FEED'
										);
									}

									if ($include)
										$this->ymlNode($node->addChild($child_node_name, $value), $definition, $child_source);
								}
							}
							else
							{
								$include = true;
								if (array_key_exists('allow_exclude', $definition) && $definition['allow_exclude'])
								{
									$include = (bool)SYAConfigurationTools::get(
										'MARKET_'.Tools::strtoupper($name).'_INCLUDE_TO_FEED'
									);
								}

								if ($include)
									$this->ymlNode($node->addChild($child_node_name), $definition, $child_source);
							}
						}
					}
					break;
			}
		}
	}

	/**
	 * @param $name
	 * @param $definition
	 * @param $source
	 * @return mixed
	 */
	protected function getValueByDefinition($name, $definition, $source)
	{
		$value = null;
		if (is_string($definition) || is_numeric($definition))
			$value = $definition;
		elseif (array_key_exists('constant', $definition))
			$value = $definition['constant'];
		elseif (array_key_exists('getter', $definition))
		{
			$value = null;
			if ($source
				&& method_exists($source, 'getOverrideValue')
				&& (!array_key_exists('override', $definition) || $definition['override']))
				$value = $source->getOverrideValue($name);

			if (null == $value)
			{
				if (is_string($definition['getter']))
					$getter = $definition['getter'];
				else
					$getter = 'get'.Tools::toCamelCase(str_replace('-', '_', $name), true);

				$value = $this->callGetter(
						$getter,
						$source
				);
			}
		}
		else if (array_key_exists('property', $definition))
		{
			if (is_string($definition['property']))
				$property = $definition['property'];
			else
				$property = $name;

			if (is_array($source) && array_key_exists($property, $source))
				$value = $source[$property];
			elseif (!is_array($source))
				$value = $source->$property;
		}
		else if (array_key_exists('module_configuration', $definition))
			$value = SYAConfigurationTools::get($definition['module_configuration']);
		else if (array_key_exists('configuration', $definition))
			$value = Configuration::get($definition['configuration']);
		else if (array_key_exists('callback', $definition))
			$value = call_user_func_array($definition['callback'], array($name, $definition, $source));

		if ($value !== null)
		{
			$type = is_array($definition) && array_key_exists('type', $definition) ? $definition['type'] : 'string';
			if (is_array($definition) && array_key_exists('xml_type', $definition))
				$type = $definition['xml_type'];

			SYATools::strictType($value, $type);

			switch ($type)
			{
				case 'date':
					$value = date($definition['format'], $value);
					break;
				case 'bool':
					$value = $value ? 'true' : 'false';
					break;
				default:
					break;
			}
		}

		return $value;
	}

	/**
	 * @param SimpleXMLElement $node
	 * @param $attributes
	 * @param $source = null
	 */
	protected function ymlAttrs(SimpleXMLElement $node, $attributes, $source = null)
	{
		foreach ($attributes as $name => $definition)
		{
			$value = null;
			if (array_key_exists('value', $definition))
				$value = $this->getValueByDefinition($name, $definition['value'], $source);

			if (array_key_exists('required', $definition) && $definition['required'] && null === $value)
				throw new LogicException(sprintf('Required value "%s" is empty', $name));

			if ($value)
				$node->addAttribute($name, $value);
		}
	}

	/**
	 * @param $getter
	 * @param $source
	 * @return mixed
	 * @throws Exception
	 */
	protected function callGetter($getter, $source)
	{
		if (method_exists($this, $getter))
			return call_user_func_array(array($this, $getter), array($source));
		elseif (method_exists($source, $getter))
			return call_user_func_array(array($source, $getter), array());
		elseif (function_exists($getter))
			return call_user_func_array($getter, array($source));
		else
			throw new LogicException(sprintf('Getter not exists "%s"', $getter));
	}

	/**
	 * @return string
	 */
	protected function getType()
	{
		$behaviour = SYAConfigurationTools::get('MARKET_TYPE_DEFAULT_BEHAVIOUR');
		return ($behaviour != 'none' ? $behaviour : '');
	}

	/**
	 * @return string
	 */
	protected function exportXml()
	{
		if (_PS_MODE_DEV_)
		{
			$dom = new DOMDocument('1.0');
			// PrestaShop validator so stupid.... sorry for that
			$dom->{'preserveWhiteSpace'} = false;
			$dom->{'formatOutput'} = true;
			$dom->encoding = 'UTF-8';
			$dom->loadXML($this->xml->asXML());

			return $dom->saveXML();
		}

		return $this->xml->asXML();
	}

	/**
	 * @return array
	 */
	protected function getCurrencies()
	{
		$export_all_currencies = (bool)SYAConfigurationTools::get(
			'MARKET_EXPORT_ALL_CURRENCIES'
		);

		if ($export_all_currencies)
			return Currency::getCurrencies();

		$currency = new Currency(
			Configuration::get('PS_CURRENCY_DEFAULT')
		);

		return array(
			array(
					'iso_code' => $currency->iso_code,
					'conversion_rate' => $currency->conversion_rate
			)
		);
	}

	/**
	 * @return array|false|mysqli_result|null|PDOStatement|resource
	 * @throws PrestaShopDatabaseException
	 */
	public function getCategories()
	{
		$active_only = (bool)SYAConfigurationTools::get('MARKET_EXCLUDE_DISABLED_CATEGORIES');
		$home_category_as_root = (bool)SYAConfigurationTools::get('MARKET_HOME_CATEGORY_AS_ROOT');

		$sql = new DbQuery();
		$sql->select('c.`id_category`, cl.`name`, c.`id_parent`');
		$sql->from('category', 'c');
		$sql->leftJoin(
			'category_lang',
			'cl',
			'c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl')
		);
		$sql->where('cl.`id_lang` = '.(int)Context::getContext()->language->id);
		$sql->where('cl.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY'));
		if ($home_category_as_root)
			$sql->where('cl.`id_category` != '.(int)Configuration::get('PS_HOME_CATEGORY'));

		if ($active_only)
			$sql->where('c.`active` = 1');

		$sql->orderBy('c.`id_category`');

		$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		$clear_category = (int)Configuration::get(
			$home_category_as_root ? 'PS_HOME_CATEGORY' : 'PS_ROOT_CATEGORY'
		);

		$category_aliases = SYAMarket::getCategoriesAliases();
		foreach ($categories as &$category)
		{
			$category['id_parent'] = (int)$category['id_parent'];
			if (array_key_exists((int)$category['id_category'], $category_aliases))
				$category['name'] = $category_aliases[$category['id_category']];
			if ($clear_category === $category['id_parent'])
				unset($category['id_parent']);
		}

		return $categories;
	}

	/**
	 * @return SYAMarketOffer[]
	 */
	protected function getOffers()
	{
		/** @var SYAMarketOffer[]  $offers */
		$offers = array();
		$ids = $this->finder->findProductIdsByFilter(SYAMarket::getProductsFilterConfiguration());
		$only_active_categories = (bool)SYAConfigurationTools::get('MARKET_EXCLUDE_DISABLED_CATEGORIES');
		$only_active_products = (bool)SYAConfigurationTools::get('MARKET_EXCLUDE_DISABLED_PRODUCTS');
		$only_available = (bool)SYAConfigurationTools::get('MARKET_EXCLUDE_NOT_AVAILABLE');

		if (SYAConfigurationTools::get('MARKET_EXPORT_COMBINATIONS'))
		{
			foreach ($ids as $id)
			{
				$pas = Product::getProductAttributesIds($id);

				if (count($pas))
				{
					foreach ($pas as $row)
					{
						$ipa = (int)$row['id_product_attribute'];
						if ($ipa)
							$offers[] = new SYAMarketOffer($id, $ipa);
					}
					unset($ipa);
				}
				else
					$offers[] = new SYAMarketOffer($id);
			}
		}
		else
		{
			foreach ($ids as $id)
				$offers[] = new SYAMarketOffer($id);
		}

		// FixMe: Можно было бы и оптимизировать выбирая сразу только нужные
		foreach ($offers as $key => $offer)
		{
			$unset = false;
			if (!$unset && $only_active_categories)
				$unset = !$this->isCategoryActive($offer->id_category_default);

			if (!$unset && $only_active_products)
				$unset = !$offer->active;

			if (!$unset && $only_available)
				$unset = !$offer->getRealAvailable();

			if ($unset)
				unset($offers[$key]);
		}

		return $offers;
	}

	/**
	 * @return string
	 */
	protected function getShopURL()
	{
		return Context::getContext()->link->getPageLink('index');
	}

	/**
	 * @param $id_category
	 * @return bool
	 */
	protected function isCategoryActive($id_category)
	{
		$id_category = (int)$id_category;
		if (!$id_category)
			return false;

		$cache_key = 'isCategoryActive._'.$id_category;
		if (!$this->hasCache($cache_key))
		{
			$active = (bool)Db::getInstance()->getValue(
				'SELECT `active` FROM `'._DB_PREFIX_.'category` WHERE `id_category` = '.$id_category
			);
			$this->setCache($cache_key, $active);
		}

		return $this->getCache($cache_key);
	}

	/**
	 * @return string
	 */
	protected function getDefaultCurrencyIsoCode()
	{
		$cache_key = 'getDefaultCurrencyIsoCode';
		if (!$this->hasCache($cache_key))
		{
			$currency = new Currency(
					(int)Configuration::get('PS_CURRENCY_DEFAULT')
			);

			$this->setCache($cache_key, $currency->iso_code);
		}

		return $this->getCache($cache_key);
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasCache($key)
	{
		return array_key_exists($key, self::$cache);
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getCache($key)
	{
		return self::$cache[$key];
	}

	/**
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function setCache($key, $value)
	{
		return self::$cache[$key] = $value;
	}
}