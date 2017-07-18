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
 * Class SYAObjectFinder
 * @version
 */
class SYAObjectFinder
{
	/**
	 * @var array
	 */
	protected static $array_cache = array();

	/**
	 * @var bool
	 */
	protected $ignore_case = false;

	/**
	 * @param bool $ignore_case
	 */
	public function __construct($ignore_case = true)
	{
		$this->ignore_case = $ignore_case;
	}

	/**
	 * @return boolean
	 */
	public function isIgnoreCase()
	{
		return $this->ignore_case;
	}

	/**
	 * @param boolean $ignore_case
	 * @return $this
	 */
	public function setIgnoreCase($ignore_case)
	{
		$this->ignore_case = $ignore_case;
		return $this;
	}

	/**
	 * @param array|string $sql
	 * @return string
	 */
	protected function getValue($sql)
	{
		if (is_array($sql))
			$sql = implode(PHP_EOL, $sql);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	/**
	 * @param array|string $sql
	 * @return string
	 */
	protected function executeS($sql)
	{
		if (is_array($sql))
			$sql = implode(PHP_EOL, $sql);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	/**
	 * @param array|string $sql
	 * @param string $column
	 * @return array
	 * @throws PrestaShopDatabaseException
	 */
	protected function getColumn($sql, $column)
	{
		if (is_array($sql))
			$sql = implode(PHP_EOL, $sql);

		$result = array();
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $row)
		{
			if (!array_key_exists($column, $row))
				throw new LogicException(sprintf('No column %s in query.', $column));

			$result[] = $row[$column];
		}

		return $result;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return string
	 */
	protected function equalsExpr($key, $value)
	{
		if ($this->ignore_case)
		{
			$value = Tools::strtolower($value);
			return pSQL($key).' = "'.pSQL($value).'"';
		}

		return 'BINARY '.pSQL($key).' = "'.pSQL($value).'"';
	}

	/**
	 * @param $class
	 * @param $id_object
	 * @return bool
	 */
	public function isObjectExists($class, $id_object)
	{
		$definition = SYATools::staticGet($class, 'definition');
		$table = $definition['table'];
		$primary = $definition['primary'];

		$sql = array();
		$sql[] = 'SELECT 1 FROM `'._DB_PREFIX_.pSQL($table).'`';
		$sql[] = 'WHERE '.pSQL($primary).' = '.(int)$id_object.'';

		return (bool)$this->getValue($sql);
	}

	/**
	 * @param $id_manufacturer
	 * @return bool
	 */
	public function isManufacturerExist($id_manufacturer)
	{
		return $this->isObjectExists('Manufacturer', $id_manufacturer);
	}

	/**
	 * @param $id_tax_rule_group
	 * @return bool
	 */
	public function isTaxRuleGroupExist($id_tax_rule_group)
	{
		return $this->isObjectExists('TaxRulesGroup', $id_tax_rule_group);
	}

	/**
	 * @param $id_category
	 * @return bool
	 */
	public function isCategoryExists($id_category)
	{
		return $this->isObjectExists('Category', $id_category);
	}

	/**
	 * @param $id_product
	 * @return bool
	 */
	public function isProductExists($id_product)
	{
		return $this->isObjectExists('Product', $id_product);
	}

	/**
	 * @param $id_category
	 * @return int
	 */
	public function findCategoryParentId($id_category)
	{
		$sql = array();
		$sql[] = 'SELECT `id_parent` FROM `'._DB_PREFIX_.'category`';
		$sql[] = 'WHERE `id_category` = '.(int)$id_category.'';

		return (int)$this->getValue($sql);
	}

	/**
	 * @param $id_supplier
	 * @return bool
	 */
	public function isSupplierExist($id_supplier)
	{
		return $this->isObjectExists('Supplier', $id_supplier);
	}

	/**
	 * @param $name
	 * @return int
	 */
	public function findManufacturerIdByName($name)
	{
		$sql = array();
		$sql[] = 'SELECT m.id_manufacturer FROM `'._DB_PREFIX_.'manufacturer` m';
		$sql[] = 'WHERE '.$this->equalsExpr('m.name', $name);

		return (int)$this->getValue($sql);
	}

	/**
	 * @param int $id_attribute_group
	 * @param string $value
	 * @param int|null $id_lang
	 * @return int
	 */
	public function findIdAttributeByValue($id_attribute_group, $value, $id_lang = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = array();
		$sql[] = 'SELECT a.`id_attribute` FROM `'._DB_PREFIX_.'attribute` a';
		$sql[] = 'LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al';
		$sql[] = 'ON al.`id_attribute` = a.`id_attribute`';
		$sql[] = 'AND al.`id_lang` = '.(int)$id_lang;
		$sql[] = 'WHERE '.$this->equalsExpr('al.`name`', $value);
		$sql[] = 'AND a.`id_attribute_group` = '.(int)$id_attribute_group;

		return (int)$this->getValue($sql);
	}

	/**
	 * @param $id_attribute_group
	 * @return int
	 */
	public function getNbAttributesInGroup($id_attribute_group)
	{
		$sql = array();
		$sql[] = 'SELECT COUNT(a.`id_attribute`) FROM `'._DB_PREFIX_.'attribute` a';
		$sql[] = 'WHERE a.`id_attribute_group` = '.(int)$id_attribute_group;

		return (int)$this->getValue($sql);
	}

	/**
	 * @param $name
	 * @return int
	 */
	public function findTaxRuleGroupIdByName($name)
	{
		$sql = array();
		$sql[] = 'SELECT trg.id_tax_rules_group FROM `'._DB_PREFIX_.'tax_rules_group` trg';
		$sql[] = 'WHERE '.$this->equalsExpr('trg.name', $name);

		return (int)$this->getValue($sql);
	}

	/**
	 * @param $name
	 * @return int
	 */
	public function findSupplierIdByName($name)
	{
		$sql = array();
		$sql[] = 'SELECT s.id_supplier FROM `'._DB_PREFIX_.'supplier` s';
		$sql[] = 'WHERE '.$this->equalsExpr('s.name', $name);

		return (int)$this->getValue($sql);
	}

	/**
	 * @param string $name
	 * @param int|null $id_parent
	 * @param int|null $id_lang
	 * @param int|null $id_shop
	 * @return int
	 */
	public function findCategoryIdByNameAndParent($name, $id_parent = null, $id_lang = null, $id_shop = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		if (null === $id_shop)
			$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

		if (null === $id_parent)
			$id_parent = (int)Configuration::get('PS_HOME_CATEGORY');

		$sql = array();
		$sql[] = 'SELECT c.id_category FROM `'._DB_PREFIX_.'category` c';
		$sql[] = 'LEFT JOIN `'._DB_PREFIX_.'category_lang` cl';
		$sql[] = 'ON cl.id_category = c.id_category';
		$sql[] = 'WHERE cl.id_lang = '.(int)$id_lang;
		$sql[] = 'AND cl.id_shop = '.(int)$id_shop;
		$sql[] = 'AND '.$this->equalsExpr('cl.name', $name);
		$sql[] = 'AND c.id_parent = "'.(int)$id_parent.'"';

		return (int)$this->getValue($sql);
	}

	/**
	 * @param $id_feature
	 * @param int|null $id_lang
	 * @return string
	 */
	public function getFeatureNameById($id_feature, $id_lang = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = array();
		$sql[] = 'SELECT fl.`name` FROM `'._DB_PREFIX_.'feature_lang` fl';
		$sql[] = 'WHERE fl.id_feature ='.(int)$id_feature;
		$sql[] = 'AND fl.id_lang ='.(int)$id_lang;

		return $this->getValue($sql);
	}

	/**
	 * @param $id_attribute_group
	 * @param int|null $id_lang
	 * @return string
	 */
	public function getAttributeGroupNameById($id_attribute_group, $id_lang = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = array();
		$sql[] = 'SELECT agl.`name` FROM `'._DB_PREFIX_.'attribute_group_lang` agl';
		$sql[] = 'WHERE agl.id_attribute_group ='.(int)$id_attribute_group;
		$sql[] = 'AND agl.id_lang ='.(int)$id_lang;

		return $this->getValue($sql);
	}

	/**
	 * @param int $id_feature
	 * @param string $value
	 * @param int|null $id_lang
	 * @return int
	 */
	public function findFeatureValueId($id_feature, $value, $id_lang = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = array();
		$sql[] = 'SELECT fv.id_feature_value FROM `'._DB_PREFIX_.'feature` f';
		$sql[] = 'LEFT JOIN '._DB_PREFIX_.'feature_value fv';
		$sql[] = 'ON f.id_feature = fv.id_feature';
		$sql[] = 'LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl';
		$sql[] = 'ON fv.id_feature_value = fvl.id_feature_value';
		$sql[] = 'WHERE fv.id_feature = '.(int)$id_feature;
		$sql[] = 'AND fv.custom = 0';
		$sql[] = 'AND fvl.id_lang = '.(int)$id_lang;
		$sql[] = 'AND '.$this->equalsExpr('fvl.value', $value);

		return (int)$this->getValue($sql);
	}

	/**
	 * @param string $property
	 * @param mixed $value
	 * @param int|null $id_lang
	 * @param int|null $id_shop
	 * @return null|Product
	 */
	public function findProductByProperty($property, $value, $id_lang = null, $id_shop = null)
	{
		$id = $this->findProductIdByProperty($property, $value, $id_lang, $id_shop);

		return $id ? new Product($id, false) : null;
	}

	/**
	 * @param $property
	 * @param $value
	 * @param int|null $id_lang
	 * @param int|null $id_shop
	 * @return int
	 */
	public function findProductIdByProperty($property, $value, $id_lang = null, $id_shop = null)
	{
		if ($property === 'id')
		{
			if ($this->isProductExists($value))
				return (int)$value;

			return 0;
		}

		$property_info = Product::$definition['fields'][$property];
		$is_lang = array_key_exists('lang', $property_info) && $property_info['lang'];
		$is_shop = array_key_exists('shop', $property_info) && $property_info['shop'];

		$table = 'product';
		if ($is_lang)
			$table .= '_lang';
		elseif ($is_shop)
			$table .= '_shop';

		$sql = array();
		$sql[] = 'SELECT a.`id_product` FROM `'._DB_PREFIX_.pSQL($table).'` a';
		if ($is_lang || $is_shop)
			$sql[] = 'RIGHT JOIN `'._DB_PREFIX_.'product` b ON a.`id_product` = b.`id_product`';

		$sql[] = 'WHERE '.$this->equalsExpr($property, $value);

		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		if (!$id_shop)
			$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

		if ($is_lang)
		{
			$sql[] = 'AND a.`id_lang` = '.(int)$id_lang;
			$sql[] = 'AND a.`id_shop` = '.(int)$id_shop;
		}

		if ($is_shop)
			$sql[] = 'AND a.`id_shop` = '.(int)$id_shop;

		return (int)$this->getValue($sql);
	}

	/**
	 * @param $id_product
	 * @return int
	 */
	public function getNbProductImages($id_product)
	{
		$sql = array(
			'SELECT COUNT(*)',
			'FROM `'._DB_PREFIX_.'image` i',
			Shop::addSqlAssociation('image', 'i'),
			'WHERE i.`id_product` = '.(int)$id_product
		);

		return (int)$this->getValue($sql);
	}

	/**
	 * @param $id_product
	 * @param $id_shop
	 * @return bool
	 */
	public function isProductHasCover($id_product, $id_shop)
	{
		$sql = array(
			'SELECT 1',
			'FROM `'._DB_PREFIX_.'image` i',
			Shop::addSqlAssociation('image', 'i'),
			'WHERE i.`id_product` = '.(int)$id_product,
			'AND image_shop.`id_shop` = '.(int)$id_shop,
			'AND image_shop.`cover` = 1',
		);

		return (bool)$this->getValue($sql);
	}

	/**
	 * @param $id_product
	 * @param array $find_by
	 * @param $exact_match
	 * @return array
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function findCombinationsIds($id_product, array $find_by, $exact_match)
	{
		$languages = array();
		foreach ($find_by as $where)
			$languages[] = (int)$where['id_lang'];
		$languages = implode(', ', array_unique($languages));

		$sql = array();
		$sql[] = 'SELECT ag.`id_attribute_group`, a.`id_attribute`, al.`name` AS attribute_name,';
		$sql[] = '	al.`id_lang`, product_attribute_shop.`id_product_attribute`';
		$sql[] = 'FROM `'._DB_PREFIX_.'product_attribute` pa';
		$sql[] = Shop::addSqlAssociation('product_attribute', 'pa', false);
		$sql[] = '	LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac';
		$sql[] = '		ON (pac.`id_product_attribute` = pa.`id_product_attribute`)';
		$sql[] = '	LEFT JOIN `'._DB_PREFIX_.'attribute` a';
		$sql[] = '		ON (a.`id_attribute` = pac.`id_attribute`)';
		$sql[] = '	LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag';
		$sql[] = '		ON (ag.`id_attribute_group` = a.`id_attribute_group`)';
		$sql[] = '	LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al';
		$sql[] = '		ON (a.`id_attribute` = al.`id_attribute`)';
		$sql[] = Shop::addSqlAssociation('attribute', 'a');
		$sql[] = 'WHERE pa.`id_product` = '.(int)$id_product;
		$sql[] = 'AND al.`id_lang` IN ('.$languages.')';
		$sql = implode(PHP_EOL, $sql);

		$combinations = array();
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $row)
		{
			$id_product_attribute = (int)$row['id_product_attribute'];
			$id_attribute_group = (int)$row['id_attribute_group'];
			if (!array_key_exists($id_product_attribute, $combinations))
				$combinations[$id_product_attribute] = array(
					'id_product_attribute' => $id_product_attribute,
					'attributes' => array(),
				);

			$id_lang = (int)$row['id_lang'];

			if (!array_key_exists($id_attribute_group, $combinations[$id_product_attribute]['attributes']))
				$combinations[$id_product_attribute]['attributes'][$id_attribute_group] = array();

			$combinations[$id_product_attribute]['attributes'][$id_attribute_group][$id_lang] = $row['attribute_name'];
		}

		$result_ids = array();
		if ($exact_match)
		{
			$nb_where = (int)count($find_by);
			foreach ($combinations as $key => $combination)
			{
				if ((int)count($combination['attributes']) !== $nb_where)
				{
					unset($combinations[$key]);
					continue;
				}
			}
		}

		foreach ($find_by as $where)
		{
			$id_lang = (int)$where['id_lang'];
			$id_attribute_group = (int)$where['id_attribute_group'];
			$value = $where['value'];
			$combination_value = null;

			foreach ($combinations as $key => $combination)
			{
				if (array_key_exists($id_attribute_group, $combination['attributes']))
					if (array_key_exists($id_lang, $combination['attributes'][$id_attribute_group]))
						$combination_value = $combination['attributes'][$id_attribute_group][$id_lang];

				if (null === $combination_value)
				{
					unset($combinations[$key]);
					continue;
				}

				if ($this->ignore_case)
				{
					$combination_value = Tools::strtolower($combination_value);
					$value = Tools::strtolower($value);
				}

				if ($value !== $combination_value)
				{
					unset($combinations[$key]);
					continue;
				}
			}
		}

		foreach ($combinations as $combination)
			$result_ids[] = $combination['id_product_attribute'];

		return $result_ids;
	}

	/**
	 * @param $id_product
	 * @param array $find_by
	 * @param $exact_match
	 * @return array
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function findCombinations($id_product, array $find_by, $exact_match)
	{
		$result_ids = $this->findCombinationsIds($id_product, $find_by, $exact_match);

		if (!count($result_ids))
			return array();

		$collection = ExcelObjectModel::collectionByClass('Combination');
		$collection->where('id_product_attribute', 'IN', $result_ids);

		return $collection->getResults();
	}

	/**
	 * @param $id_product
	 * @param int|null $id_combination
	 * @param int|null $id_shop
	 * @return int|null
	 */
	public function getIdSpecificPriceExistsForProperties($id_product, $id_combination = null, $id_shop = null)
	{
		$sql = 'SELECT `id_specific_price` FROM `'._DB_PREFIX_.'specific_price`
					WHERE `id_product`='.(int)$id_product.' AND
						`id_product_attribute`='.(int)$id_combination.' AND
						`id_shop`='.(int)$id_shop.' AND
						`id_group`= 0 AND
						`id_country`= 0 AND
						`id_currency`= 0 AND
						`id_customer`= 0 AND
						`from_quantity`= 1 AND
						`from` = \'0000-00-00 00:00:00\' AND
						 `to` = \'0000-00-00 00:00:00\' AND
						 `id_specific_price_rule` = 0
		';

		$id_specific_price = (int)$this->getValue($sql);

		return $id_specific_price ? (int)$id_specific_price : null;
	}

	/**
	 * @param string $query
	 * @param int $id_lang
	 * @return array
	 */
	public function findProducts($query, $id_lang = null)
	{
		return $this->getIdsAndNamesByQuery('Product', $query, $id_lang);
	}

	/**
	 * @param string $query
	 * @param int $id_lang
	 * @return array
	 */
	public function findCategories($query, $id_lang = null)
	{
		return $this->getIdsAndNamesByQuery('Category', $query, $id_lang);
	}

	/**
	 * @param string $query
	 * @return array
	 */
	public function findManufacturers($query)
	{
		return $this->getIdsAndNamesByQuery('Manufacturer', $query);
	}

	/**
	 * @param string $query
	 * @return array
	 */
	public function findSuppliers($query)
	{
		return $this->getIdsAndNamesByQuery('Supplier', $query);
	}

	/**
	 * @param string $class
	 * @param string $query
	 * @param int $id_lang
	 * @param int $id_shop
	 * @return array
	 */
	protected function getIdsAndNamesByQuery($class, $query, $id_lang = null, $id_shop = null)
	{
		$definition = SYATools::staticGet($class, 'definition');
		$table = $definition['table'];
		$primary = $definition['primary'];
		$multilang = array_key_exists('lang', $definition['fields']['name']) && $definition['fields']['name']['lang'];
		$multilang_shop = array_key_exists('multilang_shop', $definition) && $definition['multilang_shop'];

		$sql = array();
		$sql[] = 'SELECT a.`'.$primary.'` as id, ';
		$sql[] = ($multilang ? 'al' : 'a').'.`name`';
		$sql[] = 'FROM `'._DB_PREFIX_.$table.'` as a';
		if ($multilang)
		{
			if (null === $id_lang)
				$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

			$sql[] = 'LEFT JOIN `'._DB_PREFIX_.$table.'_lang` as al';
			$sql[] = 'ON a.`'.$primary.'` = al.`'.$primary.'`';
			$sql[] = 'AND al.`id_lang` = '.(int)$id_lang;
			if ($multilang_shop)
			{
				if (null === $id_shop)
					$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

				$sql[] = 'AND al.`id_shop` = '.(int)$id_shop;
			}
		}

		if ($query)
			$sql[] = 'WHERE '.($multilang ? 'al' : 'a').'.`name` LIKE "%'.pSQL($query).'%"';

		$sql = implode(PHP_EOL, $sql);

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		foreach ($result as &$row)
			$row['id'] = (int)$row['id'];
		unset($row);

		return $result;
	}

	/**
	 * @param null|bool $active
	 * @return array
	 */
	public function findAllProductIds($active = null)
	{
		$sql = array();
		$sql[] = 'SELECT `id_product` FROM `'._DB_PREFIX_.'product`';

		if (null !== $active)
			$sql[] = 'WHERE `active` = '.(int)$active;

		return $this->getColumn($sql, 'id_product');
	}


	/**
	 * @param null|bool $active
	 * @return array
	 */
	public function findAllCategoriesIds($active = null)
	{
		$sql = array();
		$sql[] = 'SELECT `id_category` FROM `'._DB_PREFIX_.'category`';

		if (null !== $active)
			$sql[] = 'WHERE `active` = '.(int)$active;

		return $this->getColumn($sql, 'id_category');
	}

	/**
	 * @param int $id_category
	 * @return array
	 */
	public function findAllProductIdsInCategory($id_category)
	{
		$sql = array();
		$sql[] = 'SELECT `id_product` FROM `'._DB_PREFIX_.'category_product`';
		$sql[] = 'WHERE `id_category` = '.(int)$id_category;

		return $this->getColumn($sql, 'id_product');
	}

	/**
	 * @param int $id_manufacturer
	 * @return array
	 */
	public function findAllProductIdsByManufacturer($id_manufacturer)
	{
		$sql = array();
		$sql[] = 'SELECT `id_product` FROM `'._DB_PREFIX_.'product`';
		$sql[] = 'WHERE `id_manufacturer` = '.(int)$id_manufacturer;

		return $this->getColumn($sql, 'id_product');
	}

	/**
	 * @param int $id_supplier
	 * @return array
	 */
	public function findAllProductIdsBySupplier($id_supplier)
	{
		$sql = array();
		$sql[] = 'SELECT `id_product` FROM `'._DB_PREFIX_.'product`';
		$sql[] = 'WHERE `id_supplier` = '.(int)$id_supplier;

		return $this->getColumn($sql, 'id_product');
	}

	/**
	 * @param $id_product
	 * @param $id_feature
	 * @param int|null $id_lang
	 * @return array
	 */
	protected function getFeatureValuesForProductFromDB($id_product, $id_feature, $id_lang = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = array();
		$sql[] = 'SELECT fvl.`value` FROM `'._DB_PREFIX_.'feature_product` fp';
		$sql[] = 'LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl';
		$sql[] = 'ON fvl.`id_feature_value` = fp.`id_feature_value`';
		$sql[] = 'AND fvl.`id_lang` = '.(int)$id_lang;
		$sql[] = 'WHERE fp.`id_product` = '.(int)$id_product;
		$sql[] = 'AND fp.`id_feature` = '.(int)$id_feature;

		return $this->executeS($sql);
	}

	/**
	 * @param int $id_product
	 * @param int $id_feature
	 * @param int|null $id_lang
	 * @return string
	 */
	public function getFeatureValueForProduct($id_product, $id_feature, $id_lang = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$values = $this->getFeatureValuesForProductFromDB($id_product, $id_feature, $id_lang);
		if (!$values)
			return null;

		$first = array_shift($values);

		return $first['value'];
	}

	/**
	 * @param $id_product
	 * @param $id_feature
	 * @param int|null $id_lang
	 * @return array
	 */
	public function getMultipleFeatureValuesForProduct($id_product, $id_feature, $id_lang = null)
	{
		$rows = $this->getFeatureValuesForProductFromDB($id_product, $id_feature, $id_lang);

		$result = array();
		foreach ($rows as $row)
			$result[] = $row['value'];

		return $result;
	}

	/**
	 * @param int $id_product_attribute
	 * @param int $id_attribute_group
	 * @param int|null $id_lang
	 * @return string
	 */
	public function getAttributeGroupValueForCombination($id_product_attribute, $id_attribute_group, $id_lang = null)
	{
		if (null === $id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = array();
		$sql[] = 'SELECT al.`name` FROM `'._DB_PREFIX_.'product_attribute_combination` pac';
		$sql[] = 'LEFT JOIN `'._DB_PREFIX_.'attribute` a';
		$sql[] = 'ON a.`id_attribute` = pac.`id_attribute`';
		$sql[] = 'LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al';
		$sql[] = 'ON al.`id_attribute` = a.`id_attribute`';
		$sql[] = 'AND al.`id_lang` = '.(int)$id_lang;
		$sql[] = 'WHERE pac.`id_product_attribute` = '.(int)$id_product_attribute;
		$sql[] = 'AND a.`id_attribute_group` = '.(int)$id_attribute_group;

		return $this->getValue($sql);
	}

	/**
	 * @param int $id_product
	 * @return array
	 */
	public function getManufacturerNameByProductId($id_product)
	{
		$sql = array();
		$sql[] = 'SELECT m.`name` FROM `'._DB_PREFIX_.'product` p';
		$sql[] = 'LEFT JOIN `'._DB_PREFIX_.'manufacturer` m';
		$sql[] = 'ON p.`id_manufacturer` = m.`id_manufacturer`';
		$sql[] = 'WHERE p.`id_product` = '.(int)$id_product;

		return $this->getValue($sql);
	}

	/**
	 * @param int $id_product
	 * @return array
	 */
	public function getSupplierNameByProductId($id_product)
	{
		$sql = array();
		$sql[] = 'SELECT s.`name` FROM `'._DB_PREFIX_.'product` p';
		$sql[] = 'LEFT JOIN `'._DB_PREFIX_.'supplier` s';
		$sql[] = 'ON p.`id_supplier` = s.`id_supplier`';
		$sql[] = 'WHERE p.`id_product` = '.(int)$id_product;

		return $this->getValue($sql);
	}

	/**
	 * @param int $id_tax_rules_group
	 * @return array
	 */
	public function getTaxRuleNameById($id_tax_rules_group)
	{
		if (!$id_tax_rules_group) return '';

		$sql = array();
		$sql[] = 'SELECT trg.`name` FROM `'._DB_PREFIX_.'tax_rules_group` trg';
		$sql[] = 'WHERE trg.`id_tax_rules_group` = '.(int)$id_tax_rules_group;

		return $this->getValue($sql);
	}

	/**
	 * @param array $filter
	 * @return array
	 */
	public function findProductIdsByFilter($filter)
	{
		$mode = 'all';
		$items = array();
		if (is_array($filter))
		{
			$mode = $filter['mode'];
			if (array_key_exists('items', $filter))
				$items = $filter['items'];
		}

		$ids = array();
		switch ($mode)
		{
			case 'all':
				foreach ($this->findAllProductIds(null) as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'active':
				foreach ($this->findAllProductIds(true) as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'not_active':
				foreach ($this->findAllProductIds(false) as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'by_category':
				foreach ($items as $id_category)
					foreach ($this->findAllProductIdsInCategory($id_category) as $id)
						if ((int)$id)
							$ids[] = $id;
				break;
			case 'by_manufacturer':
				foreach ($items as $id_manufacturer)
					foreach ($this->findAllProductIdsByManufacturer($id_manufacturer) as $id)
						if ((int)$id)
							$ids[] = $id;
				break;
			case 'by_supplier':
				foreach ($items as $id_supplier)
					foreach ($this->findAllProductIdsBySupplier($id_supplier) as $id)
						if ((int)$id)
							$ids[] = $id;
				break;
			case 'selected':
				foreach ($items as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'not_selected':
				$all = $this->findAllProductIds(null);
				foreach ($items as &$id)
					$id = (int)$id;
				unset($id);
				foreach ($all as $id)
					if ((int)$id && !in_array((int)$id, $items, true))
						$ids[] = $id;
				break;
			default:
				break;
		}

		$ids = array_unique($ids);
		SYATools::arrayOfIds($ids);
		sort($ids);

		return $ids;
	}

	/**
	 * @param array $filter
	 * @return array
	 */
	public function findCategoriesIdsByFilter($filter)
	{
		$mode = 'all';
		$items = array();
		if (is_array($filter))
		{
			$mode = $filter['mode'];
			if (array_key_exists('items', $filter))
				$items = $filter['items'];
		}

		$ids = array();
		switch ($mode)
		{
			case 'all':
				foreach ($this->findAllCategoriesIds(null) as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'active':
				foreach ($this->findAllCategoriesIds(true) as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'not_active':
				foreach ($this->findAllCategoriesIds(false) as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'selected':
				foreach ($items as $id)
					if ((int)$id)
						$ids[] = $id;
				break;
			case 'not_selected':
				$all = $this->findAllCategoriesIds(null);
				foreach ($items as &$id)
					$id = (int)$id;
				unset($id);
				foreach ($all as $id)
					if ((int)$id && !in_array((int)$id, $items, true))
						$ids[] = $id;
				break;
			default:
				break;
		}

		$ids = array_unique($ids);
		sort($ids);

		return $ids;
	}

	/**
	 * @param array $products_ids
	 * @return array
	 */
	public function findCombinationsIdsForProducts($products_ids)
	{
		if (!is_array($products_ids))
			$products_ids = array($products_ids);

		SYATools::arrayOfIds($products_ids);

		if (empty($products_ids))
			return array();

		$sql = array();
		$sql[] = 'SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute`';
		$sql[] = 'WHERE `id_product` IN ('.implode(',', $products_ids).')';
		$sql = implode(PHP_EOL, $sql);

		$result = array();
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $row)
			$result[] = (int)$row['id_product_attribute'];

		return $result;
	}

	/**
	 * @param $id_lang
	 * @return string
	 */
	public static function getIsoCode($id_lang)
	{
		return Language::getIsoById($id_lang);
	}

	/**
	 * @param $id_shop
	 * @return mixed
	 */
	public static function getShopName($id_shop)
	{
		$shop = Shop::getShop($id_shop);
		return $shop['name'];
	}

	/**
	 * @param array $path
	 * @param int $id_lang
	 * @param int $id_shop
	 * @return int|null
	 */
	public function findCategoryIdByNamePath($path, $id_lang = null, $id_shop = null)
	{
		$id_category = null;
		$id_parent = (int)Configuration::get('PS_HOME_CATEGORY');
		foreach ($path as $name)
		{
			$id_category = (int)$this->findCategoryIdByNameAndParent(
				$name,
				$id_parent,
				$id_lang,
				$id_shop
			);

			if ($id_category)
				$id_parent = $id_category;
			else
				return null;
		}

		return $id_category;
	}
}