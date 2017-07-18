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

/**
 * Class SYAMarket
 */
class SYAMarket extends SYAComponent
{
	/**
	 * SYAMarket constructor.
	 *
	 * @param SeoSAYandexServices $module
	 * @param Context|null $context
	 */
	public function __construct(SeoSAYandexServices $module, Context $context = null)
	{
		parent::__construct($module, $context);

		$this->display_name = $this->l('Yandex Market');
	}

	/**
	 * @return bool
	 */
	public function isEnabledByDefault()
	{
		return true;
	}

	public function getDefaults()
	{
		return array(
			'MARKET_PUBLIC_FEED' => true,
			'MARKET_SHOP_NAME' => $this->context->shop->name,
			'MARKET_EXPORT_ATTRIBUTES_IN_PARAMS' => false,
			'MARKET_SHOP_COMPANY' => false,
			'MARKET_SHOP_URL' => false,
			'MARKET_SHOP_PLATFORM' => false,
			'MARKET_SHOP_VERSION' => false,
			'MARKET_SHOP_AGENCY' => false,
			'MARKET_SHOP_EMAIL' => false,
			'MARKET_SHOP_CPA' => false,
			'MARKET_SHOP_CURRENCIES' => false,
			'MARKET_SHOP_CATEGORIES' => false,
			'MARKET_SHOP_OFFERS' => false,
			'MARKET_ID_INCLUDE_TO_FEED' => false,
			'MARKET_ID_ALLOW_OVERRIDE' => false,
			'MARKET_TYPE_INCLUDE_TO_FEED' => true,
			'MARKET_TYPE_ALLOW_OVERRIDE' => false,
			'MARKET_AVAILABLE_INCLUDE_TO_FEED' => true,
			'MARKET_AVAILABLE_ALLOW_OVERRIDE' => true,
			'MARKET_AVAILABLE_DEFAULT_BEHAVIOUR' => 'by_quantity',
			'MARKET_URL_ALLOW_OVERRIDE' => true,
			'MARKET_TYPE_DEFAULT_BEHAVIOUR' => 'none',
			'MARKET_URL_DEFAULT_BEHAVIOUR' => false,
			'MARKET_PRICE_ALLOW_OVERRIDE' => true,
			'MARKET_PRICE_DEFAULT_BEHAVIOUR' => false,
			'MARKET_OLD_PRICE_INCLUDE_TO_FEED' => null,
			'MARKET_OLD_PRICE_ALLOW_OVERRIDE' => true,
			'MARKET_OLD_PRICE_DEFAULT_BEHAVIOUR' => false,
			'MARKET_CURRENCY_ID_ALLOW_OVERRIDE' => true,
			'MARKET_CURRENCY_ID_DEFAULT_BEHAVIOUR' => false,
			'MARKET_CATEGORY_ID_ALLOW_OVERRIDE' => true,
			'MARKET_CATEGORY_ID_DEFAULT_BEHAVIOUR' => false,
			'MARKET_PICTURE_INCLUDE_TO_FEED' => true,
			'MARKET_PICTURE_ALLOW_OVERRIDE' => true,
			'MARKET_PICTURE_DEFAULT_BEHAVIOUR' => false,
			'MARKET_STORE_INCLUDE_TO_FEED' => null,
			'MARKET_STORE_DEFAULT_BEHAVIOUR' => false,
			'MARKET_PICKUP_INCLUDE_TO_FEED' => null,
			'MARKET_PICKUP_DEFAULT_BEHAVIOUR' => false,
			'MARKET_DELIVERY_INCLUDE_TO_FEED' => null,
			'MARKET_DELIVERY_DEFAULT_BEHAVIOUR' => false,
			'MARKET_LOCAL_DELIVERY_COST_INCLUDE_TO_FEED' => null,
			'MARKET_LOCAL_DELIVERY_COST_DEFAULT_BEHAVIOUR' => false,
			'MARKET_NAME_ALLOW_OVERRIDE' => true,
			'MARKET_NAME_DEFAULT_BEHAVIOUR' => false,
			'MARKET_VENDOR_INCLUDE_TO_FEED' => true,
			'MARKET_VENDOR_ALLOW_OVERRIDE' => true,
			'MARKET_VENDOR_DEFAULT_BEHAVIOUR' => false,
			'MARKET_VENDOR_CODE_INCLUDE_TO_FEED' => true,
			'MARKET_VENDOR_CODE_ALLOW_OVERRIDE' => true,
			'MARKET_VENDOR_CODE_DEFAULT_BEHAVIOUR' => 'ean13',
			'MARKET_DESCRIPTION_INCLUDE_TO_FEED' => true,
			'MARKET_DESCRIPTION_ALLOW_OVERRIDE' => true,
			'MARKET_DESCRIPTION_DEFAULT_BEHAVIOUR' => 'description_short',
			'MARKET_SALES_NOTES_INCLUDE_TO_FEED' => null,
			'MARKET_SALES_NOTES_DEFAULT_BEHAVIOUR' => false,
			'MARKET_MANUFACTURER_WARRANTY_INCLUDE_TO_FEED' => null,
			'MARKET_MANUFACTURER_WARRANTY_DEFAULT_BEHAVIOUR' => false,
			'MARKET_ADULT_INCLUDE_TO_FEED' => null,
			'MARKET_ADULT_DEFAULT_BEHAVIOUR' => false,
			'MARKET_AGE_INCLUDE_TO_FEED' => null,
			'MARKET_AGE_DEFAULT_BEHAVIOUR' => false,
			'MARKET_BARCODE_INCLUDE_TO_FEED' => null,
			'MARKET_BARCODE_ALLOW_OVERRIDE' => true,
			'MARKET_BARCODE_DEFAULT_BEHAVIOUR' => 'upc',

			'MARKET_COUNTRY_OF_ORIGIN_INCLUDE_TO_FEED' => null,
			'MARKET_COUNTRY_OF_ORIGIN_ALLOW_OVERRIDE' => true,
			'MARKET_LOCAL_DELIVERY_DAYS_INCLUDE_TO_FEED' => null,
			'MARKET_LOCAL_DELIVERY_DAYS_ALLOW_OVERRIDE' => true,
			'MARKET_TYPE_PREFIX_INCLUDE_TO_FEED' => null,
			'MARKET_TYPE_PREFIX_ALLOW_OVERRIDE' => true,
			'MARKET_REC_INCLUDE_TO_FEED' => null,
			'MARKET_REC_ALLOW_OVERRIDE' => true,
			'MARKET_EXPIRY_INCLUDE_TO_FEED' => null,
			'MARKET_EXPIRY_OVERRIDE' => true,
			'MARKET_WEIGHT_INCLUDE_TO_FEED' => null,
			'MARKET_WEIGHT_OVERRIDE' => true,
			'MARKET_DIMENSIONS_INCLUDE_TO_FEED' => null,
			'MARKET_DIMENSIONS_OVERRIDE' => true,
			'MARKET_CPA_INCLUDE_TO_FEED' => null,
			'MARKET_CPA_OVERRIDE' => true,
			'MARKET_DELIVERY-OPTIONS_INCLUDE_TO_FEED' => null,
			'MARKET_DELIVERY-OPTIONS_OVERRIDE' => true,

			'MARKET_PARAM_INCLUDE_TO_FEED' => false,
			'MARKET_PARAM_ALLOW_OVERRIDE' => false,
			'MARKET_EXPORT_COMBINATIONS' => null,
			'MARKET_EXPORT_FEATURES' => null,
			'MARKET_EXPORT_ALL_CURRENCIES' => null,
			'MARKET_EXCLUDE_NOT_AVAILABLE' => null,
			'MARKET_EXCLUDE_DISABLED_CATEGORIES' => null,
			'MARKET_GZIP' => null,
			'MARKET_PRODUCTS_FILTER_MODE' => 'all',
			'MARKET_PRODUCTS_FILTER_ITEMS' => null,

			'MARKET_OUTLETS' => ''
		);
	}

	/**
	 * @return bool
	 */
	public function install()
	{
		$sql_category_aliases = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'market_category_aliases` (
			`id_category` INT(11) NOT NULL ,
			`alias` TEXT NOT NULL
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

		$sql_market_feature = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'market_feature` (
			`id_feature` INT(11) NOT NULL ,
			`name` TEXT NOT NULL,
			`unit` TEXT NOT NULL
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

		$sql_market_attribute_group = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'market_attribute_group` (
			`id_attribute_group` INT(11) NOT NULL ,
			`name` TEXT NOT NULL,
			`unit` TEXT NOT NULL
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

		$sql_market_product_outlet = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'market_product_outlet` (
			`id_product` INT(11) NOT NULL ,
			`id` TEXT NOT NULL,
			`instock` TEXT NOT NULL,
			`booking` TEXT NOT NULL
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

		return parent::install() && $this->registerHook('actionAdminControllerSetMedia')
		&& $this->registerHook('displayAdminProductsExtra')
		&& SYAMarketProductOverride::createTable() && Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_category_aliases)
		&& Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_market_feature)
		&& Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_market_attribute_group)
		&& Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_market_product_outlet);
	}

	/**
	 * @return bool
	 */
	public function uninstall()
	{
		return parent::uninstall()
		&& SYAMarketProductOverride::dropTable() && SYADatabaseTools::dropTable('market_category_aliases', true)
		&& SYADatabaseTools::dropTable('market_feature', true) && SYADatabaseTools::dropTable('market_attribute_group', true)
		&& SYADatabaseTools::dropTable('market_product_outlet', true);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		$standard = SYAYmlStandard::getStandard();
		$catalog = &$standard['yml_catalog'];
		$shop = &$catalog['children']['shop'];
		$offer = SYAYmlStandard::getOfferStandard();

		$config = array(
			'shop' => array()
		);

		foreach ($shop['children'] as $field_name => $definition)
		{
			$config_name = 'MARKET_SHOP_'.Tools::strtoupper($field_name);
			$value = SYAConfigurationTools::get($config_name);
			SYATools::strictType($value, array_key_exists('type', $definition) ? $definition['type'] : 'string');
			$config['shop'][$field_name] = $value;
		}

		$offer_props = array_merge($offer['attributes'], $offer['children']);
		foreach ($offer_props as $field_name => $definition)
		{
			$value = array();
			if (array_key_exists('required', $definition) && $definition['required'])
				$value['include_to_feed'] = true;
			else
			{
				$conf = 'MARKET_'.Tools::strtoupper($field_name).'_INCLUDE_TO_FEED';
				$value['include_to_feed'] = (bool)SYAConfigurationTools::get($conf);
			}

			if (array_key_exists('override_only', $definition) && $definition['override_only'])
				$value['allow_override'] = true;
			else
			{
				$conf = 'MARKET_'.Tools::strtoupper($field_name).'_ALLOW_OVERRIDE';
				$value['allow_override'] = (bool)SYAConfigurationTools::get($conf);
			}

			if (array_key_exists('default_behaviour', $definition) && $definition['default_behaviour'])
			{
				$default_behaviour = $definition['default_behaviour'];
				$conf = 'MARKET_'.Tools::strtoupper($field_name).'_DEFAULT_BEHAVIOUR';
				$default_behaviour_value = SYAConfigurationTools::get($conf);
				SYATools::strictType(
					$default_behaviour_value,
					array_key_exists('type', $default_behaviour) ? $default_behaviour['type'] : 'string'
				);

				$value['default_behaviour'] = $default_behaviour_value;
			}

			$config['fields'][$field_name] = $value;
		}

		$booleans = array(
			'export_combinations',
			'export_features',
			'export_all_currencies',
			'exclude_not_available',
			'exclude_disabled_categories',
			'gzip',
			'public_feed',
			'export_attributes_in_params'
		);

		foreach ($booleans as $bool)
		{
			$config_name = 'MARKET_'.Tools::strtoupper($bool);
			$config[$bool] = (bool)SYAConfigurationTools::get($config_name);
		}

		$config['products_filter'] = self::getProductsFilterConfiguration();
		return $config;
	}

	/**
	 * @return array
	 */
	public static function getProductsFilterConfiguration()
	{
		$products_filter = array(
			'mode' => SYAConfigurationTools::get('MARKET_PRODUCTS_FILTER_MODE'),
			'items' => explode(',', SYAConfigurationTools::get('MARKET_PRODUCTS_FILTER_ITEMS')),
		);
		SYATools::arrayOfIds($products_filter['items']);

		return $products_filter;
	}

	/**
	 * @return array
	 */
	public function getAngularValues()
	{
		return array(
			'market_config' => $this->getConfiguration(),
			'market_yml_standard' => SYAYmlStandard::getStandard(),
			'market_urls' => array(
				'dynamic' => $this->context->link->getModuleLink(
					$this->module->name,
					'front',
					array(
							'component' => $this->getName(),
							'component_controller' => 'feed',
					)
				),
				'cron' => $this->context->link->getModuleLink(
					$this->module->name,
					'front',
					array(
						'component' => $this->getName(),
						'component_controller' => 'feed',
						'cron' => true,
					)
				),
			),
			'category_aliases' => self::getCategoriesAliases(),
			'market_features' => self::getMarketFeatures(),
			'market_attribute_groups' => self::getMarketAttributeGroups(),
			'categories' => $this->getAngularCategories(),
			'features' => $this->getAngularFeatures(),
			'attribute_groups' => $this->getAngularAttributeGroups(),
			'outlets' => $this->getAngularOutlets()
		);
	}

	/**
	 * @return string
	 * @throws \Exception
	 * @throws \SmartyException
	 */
	public function hookDisplayAdminProductsExtra()
	{
		if (!Tools::isSubmit('updateproduct'))
			return;

		if (!Tools::getValue('id_product'))
			return;

		SeoSAYandexServices::registerSmartyFunctions();

		$id_product = (int)Tools::getValue('id_product');

		$this->context->smarty->assign(
			'market_product_overrides',
			SYAMarketProductOverride::getByProductId($id_product)->getOverrides()
		);

		$this->context->smarty->assign(
			'product_outlets',
			self::getProductOutlets($id_product)
		);

		return $this->render($this->getAdminTemplatePath('products_extra.tpl'));
	}

	/**
	 * @void
	 */
	public function hookDisplayBackOfficeHeader()
	{
		return $this->hookActionAdminControllerSetMedia();
	}

	/**
	 * @void
	 */
	public function hookActionAdminControllerSetMedia()
	{
		if (!$this->context->controller instanceof AdminProductsController)
			return;

		if (!Tools::isSubmit('updateproduct'))
			return;

		if (!Tools::getValue('id_product'))
			return;

		$this->module->loadAngularApp();
	}

	/**
	 * @return mixed
	 */
	public function ajaxProcessUpdateOverrideValue()
	{
		$id_product = (int)SYAJSONRequest::getValue('id_product');
		$override = SYAMarketProductOverride::getByProductId($id_product);

		$name = SYAJSONRequest::getValue('name');
		$value = SYAJSONRequest::getValue('value');

		$override->updateOverrideValue($name, $value);

		return $override->save();
	}

	/**
	 * @return mixed
	 */
	public function ajaxProcessGenerateStaticFeed()
	{
		$public = (bool)SYAConfigurationTools::get('MARKET_PUBLIC_FEED');
		if (!$public)
			throw new LogicException('Access Denied');

		return (bool)$this->generate((bool)SYAConfigurationTools::get('MARKET_GZIP'), true);
	}

	/**
	 * @return mixed
	 */
	public function ajaxProcessGenerateFeed()
	{
		echo $this->generate((bool)SYAConfigurationTools::get('MARKET_GZIP'), false);

		exit;
	}

	/**
	 * @return mixed
	 */
	public function ajaxProcessSetGzip()
	{
		$this->clearStaticFiles();

		return SYAConfigurationTools::update('MARKET_GZIP', SYAJSONRequest::getValue('value'));
	}

	/**
	 * @return mixed
	 */
	public function ajaxProcessSetPublicFeed()
	{
		$this->clearStaticFiles();

		return SYAConfigurationTools::update('MARKET_PUBLIC_FEED', SYAJSONRequest::getValue('value'));
	}

	public function ajaxProcessSaveCategoryAliases()
	{
		$category_aliases = SYAJSONRequest::getValue('category_aliases');
		self::cleanCategoryAliases();
		if (is_array($category_aliases) && count($category_aliases))
			self::addCategoryAliases($category_aliases);
		return 1;
	}

	public function ajaxProcessSaveMarketFeatures()
	{
		$market_features = SYAJSONRequest::getValue('market_features');
		self::cleanMarketFeatures();
		if (is_array($market_features) && count($market_features))
			self::addMarketFeatures($market_features);
		return 1;
	}

	public function ajaxProcessSaveMarketAttributeGroups()
	{
		$market_attribute_groups = SYAJSONRequest::getValue('market_attribute_groups');
		self::cleanMarketAttributeGroups();
		if (is_array($market_attribute_groups) && count($market_attribute_groups))
			self::addMarketAttributeGroups($market_attribute_groups);
		return 1;
	}

	public function ajaxProcessSaveMarketOutlets()
	{
		$outlets = SYAJSONRequest::getValue('outlets');
		SYAConfigurationTools::update('MARKET_OUTLETS', Tools::jsonEncode($outlets));
		return 1;
	}

	/**
	 * @return int
	 */
	public function ajaxProcessSaveProductOutlets()
	{
		$id_product = (int)SYAJSONRequest::getValue('id_product');
		$product_outlets = SYAJSONRequest::getValue('product_outlets');
		$this->cleanProductOutlets($id_product);
		$outlets = array();
		if (is_array($product_outlets) && count($product_outlets))
		{
			foreach ($product_outlets as $product_outlet)
				$outlets[] = array(
					'id_product' => $product_outlet['id_product'],
					'id' => $product_outlet['id'],
					'instock' => $product_outlet['instock'],
					'booking' => $product_outlet['booking']
				);
		}
		$this->addProductOutlets($outlets);
		return 1;
	}

	/**
	 * @return bool
	 */
	public function clearStaticFiles()
	{
		$paths = array(_PS_ROOT_DIR_.'/yml.xml.gz', _PS_ROOT_DIR_.'/yml.xml');
		foreach ($paths as $path)
			if (file_exists($path))
				unlink($path);

		return true;
	}

	/**
	 * @param bool|false $gz
	 * @param bool|false $static
	 * @return bool|string
	 */
	public function generate($gz = false, $static = false)
	{
		$generator = new SYAMarketYmlGenerator();
		$xml = $generator->generate();

		if ($gz)
		{
			$tmp_file = tempnam(sys_get_temp_dir(), 'market_yml');
			touch($tmp_file);
			$zip = new ZipArchive();
			$zip->open($tmp_file);
			$zip->addFromString('yml.xml', $xml);
			$zip->close();
			$result = call_user_func_array('file_get_contents', array($tmp_file));
			unlink($tmp_file);
		}
		else
			$result = &$xml;

		if (!$static)
			return $result;

		$path = _PS_ROOT_DIR_.'/yml.xml';
		if ($gz)
			$path .= '.gz';

		if (file_exists($path))
			unlink($path);

		return file_put_contents($path, $result) && chmod($path, 0777);
	}

	/**
	 * @param $aliases
	 *
	 * @return bool
	 */
	public function addCategoryAliases($aliases)
	{
		$insert = array();
		foreach ($aliases as $id_category => $alias)
		{
			$id_category = (int)$id_category;
			if ($id_category)
				$insert[] = array(
					'id_category' => $id_category,
					'alias' => $alias,
				);
		}

		return Db::getInstance()->insert('market_category_aliases', $insert);
	}

	/**
	 * @return bool
	 */
	public function cleanCategoryAliases()
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'market_category_aliases` WHERE 1';

		return Db::getInstance()->execute($sql);
	}

	public static $aliases_cache = null;
	/**
	 * @return array
	 */
	public static function getCategoriesAliases()
	{
		if (null === self::$aliases_cache)
		{
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'market_category_aliases`';

			self::$aliases_cache = array();
			foreach (Db::getInstance()->executeS($sql) as $row)
				self::$aliases_cache[(int)$row['id_category']] = $row['alias'];
		}

		return self::$aliases_cache;
	}

	public static $features_cache = null;
	/**
	 * @return array
	 */
	public static function getMarketFeatures()
	{
		if (null === self::$features_cache)
		{
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'market_feature`';

			self::$features_cache = array();
			foreach (Db::getInstance()->executeS($sql) as $row)
				self::$features_cache[(int)$row['id_feature']] = $row;
		}

		return self::$features_cache;
	}

	/**
	 * @param $features
	 *
	 * @return bool
	 */
	public function addMarketFeatures($features)
	{
		$insert = array();
		foreach ($features as $id_feature => $feature)
		{
			$id_feature = (int)$id_feature;
			if ($id_feature)
				$insert[] = array(
					'id_feature' => $id_feature,
					'name' => isset($feature['name']) ? $feature['name'] : '',
					'unit' => isset($feature['unit']) ? $feature['unit'] : ''
				);
		}

		return Db::getInstance()->insert('market_feature', $insert);
	}

	/**
	 * @return bool
	 */
	public function cleanMarketFeatures()
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'market_feature` WHERE 1';

		return Db::getInstance()->execute($sql);
	}

	public static $attribute_groups_cache = null;
	/**
	 * @return array
	 */
	public static function getMarketAttributeGroups()
	{
		if (null === self::$attribute_groups_cache)
		{
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'market_attribute_group`';

			self::$features_cache = array();
			foreach (Db::getInstance()->executeS($sql) as $row)
				self::$attribute_groups_cache[(int)$row['id_attribute_group']] = $row;
		}

		return self::$attribute_groups_cache;
	}

	/**
	 * @param $attribute_groups
	 *
	 * @return bool
	 */
	public function addMarketAttributeGroups($attribute_groups)
	{
		$insert = array();
		foreach ($attribute_groups as $id_attribute_group => $attribute_group)
		{
			$id_attribute_group = (int)$id_attribute_group;
			if ($id_attribute_group)
				$insert[] = array(
					'id_attribute_group' => $id_attribute_group,
					'name' => isset($attribute_group['name']) ? $attribute_group['name'] : '',
					'unit' => isset($attribute_group['unit']) ? $attribute_group['unit'] : ''
				);
		}

		return Db::getInstance()->insert('market_attribute_group', $insert);
	}

	/**
	 * @return bool
	 */
	public function cleanMarketAttributeGroups()
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'market_attribute_group` WHERE 1';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * @param $product_outlets
	 *
	 * @return bool
	 */
	public function addProductOutlets($product_outlets)
	{
		return Db::getInstance()->insert('market_product_outlet', $product_outlets);
	}

	/**
	 * @param int $id_product
	 * @return bool
	 */
	public function cleanProductOutlets($id_product)
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'market_product_outlet` WHERE `id_product` = '.(int)$id_product;

		return Db::getInstance()->execute($sql);
	}

	public static $product_outlets_cache = array();
	/**
	 * @param int $id_product
	 * @return array
	 */
	public static function getProductOutlets($id_product)
	{
		if (!isset(self::$product_outlets_cache[$id_product]))
		{
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'market_product_outlet` WHERE `id_product` = '.(int)$id_product;

			self::$product_outlets_cache[$id_product] = array();
			foreach (Db::getInstance()->executeS($sql) as $row)
				self::$product_outlets_cache[$id_product][] = $row;
		}

		return self::$product_outlets_cache[$id_product];
	}

	/**
	 * @return array
	 */
	protected function getAngularCategories()
	{
		$sql = 'SELECT ';
		$sql .= 'c.`id_category`, cl.`name`
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (c.`id_category` = cs.`id_category`'.Shop::addSqlRestrictionOnLang('cs').')
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
			ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
			'.Shop::addSqlAssociation('category', 'c', false).'
			WHERE cl.`id_lang` = '.(int)$this->context->language->id.'
			AND cs.`id_shop` IN('.implode(',', array_map('intval', Shop::getContextListShopID())).')
			AND c.`id_category` != '.Configuration::get('PS_ROOT_CATEGORY').'
			GROUP BY c.id_category
			ORDER BY c.`id_category`, category_shop.`position`
		';

		$categories = array();
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $category)
		{
			$categories[] = array(
				'id_category' => (int)$category['id_category'],
				'name' => (int)$category['id_category'].' | '.$category['name']
			);
		}

		return $categories;
	}

	/**
	 * @return array
	 */
	protected function getAngularFeatures()
	{
		$result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'feature_lang
		 WHERE id_lang = '.(int)$this->context->language->id);
		$features = array();

		if (is_array($result) && count($result))
			foreach ($result as $row)
				$features[] = array(
					'id_feature' => $row['id_feature'],
					'name' => (int)$row['id_feature'].' | '.$row['name']
				);
		return $features;
	}

	/**
	 * @return array
	 */
	protected function getAngularAttributeGroups()
	{
		$result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'attribute_group_lang
		 WHERE id_lang = '.(int)$this->context->language->id);
		$attribute_groups = array();

		if (is_array($result) && count($result))
			foreach ($result as $row)
				$attribute_groups[] = array(
					'id_attribute_group' => $row['id_attribute_group'],
					'name' => (int)$row['id_attribute_group'].' | '.$row['name']
				);
		return $attribute_groups;
	}

	protected function getAngularOutlets()
	{
		return self::getOutlets();
	}

	public static function getOutlets()
	{
		$outlets = SYAConfigurationTools::get('MARKET_OUTLETS');
		if ($outlets)
			$outlets = Tools::jsonDecode($outlets, true);
		else
			$outlets = array();
		if (!is_array($outlets))
			return array();
		return $outlets;
	}

	protected static $cache_outlets_key_category = null;
	public static function getOutletsKeyCategory()
	{
		if (is_null(self::$cache_outlets_key_category))
		{
			$outlets = self::getOutlets();
			$outlets_key_category = array();

			foreach ($outlets as $outlet)
			{
				if (!isset($outlets_key_category[$outlet['id_category']]))
					$outlets_key_category[$outlet['id_category']] = array();

				$outlets_key_category[$outlet['id_category']][] = $outlet;
			}

			self::$cache_outlets_key_category = $outlets_key_category;
		}
		return self::$cache_outlets_key_category;
	}
}