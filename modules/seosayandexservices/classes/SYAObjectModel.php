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
 * Class SYAObjectModel
 */
abstract class SYAObjectModel extends ObjectModel
{
	/***
	 * @return string
	 */
	public static function getTable()
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		return $definition['table'];
	}

	/***
	 * @return string
	 */
	public static function getPrimaryColumn()
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		return $definition['primary'];
	}

	/**
	 * @param string $field
	 * @return bool
	 */
	public static function isRequiredField($field)
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		return array_key_exists('fields', $definition)
		&& array_key_exists($field, $definition['fields'])
		&& array_key_exists('required', $definition['fields'][$field])
		&& $definition['fields'][$field]['required'];
	}

	/**
	 * @param string $field
	 * @param string $class
	 * @return bool
	 */
	public static function isLanguageField($field, $class = null)
	{
		if (null === $class)
			$class = get_called_class();
		elseif (is_object($class))
			$class = get_class($class);

		$definition = SYATools::staticGet($class, 'definition');

		return array_key_exists('fields', $definition)
		&& array_key_exists($field, $definition['fields'])
		&& array_key_exists('lang', $definition['fields'][$field])
		&& $definition['fields'][$field]['lang'];
	}

	/**
	 * @param string $field
	 * @param string $class
	 * @return bool
	 */
	public static function isShopField($field, $class = null)
	{
		if (null === $class)
			$class = get_called_class();
		elseif (is_object($class))
			$class = get_class($class);

		$definition = SYATools::staticGet($class, 'definition');
		$lang = self::isLanguageField($field, $class);
		$shop = array_key_exists('fields', $definition)
			&& array_key_exists($field, $definition['fields'])
			&& array_key_exists('shop', $definition['fields'][$field])
			&& $definition['fields'][$field]['shop'];

		return (array_key_exists('multilang_shop', $definition) && $definition['multilang_shop'] && $lang) || $shop;
	}

	/***
	 * @return array
	 */
	public static function getDefinitionFields()
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		return array_key_exists('fields', $definition) ? $definition['fields'] : array();
	}

	/**
	 * @param array &$sql
	 * @param bool $lang
	 */
	private static function iterateFieldsForSql(&$sql, $lang)
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		foreach ($definition['fields'] as $name => $field_def)
		{
			$is_lang = (bool)array_key_exists('lang', $field_def) && $field_def['lang'];
			if ($lang !== $is_lang)
				continue;

			$line = '   `'.pSQL($name).'` ';

			switch ($field_def['type'])
			{
				case self::TYPE_INT:
					$line .= 'INT(11)';
					break;
				case self::TYPE_BOOL:
					$line .= 'TINYINT(1)';
					break;
				case self::TYPE_STRING:
					$line .= 'TEXT';
					break;
				case self::TYPE_FLOAT:
					$line .= 'DECIMAL(17,6)';
					break;
				case self::TYPE_HTML:
					$line .= 'TEXT';
					break;
				case self::TYPE_DATE:
					$line .= 'DATETIME';
					break;
				case self::TYPE_NOTHING:
				default:
					$line .= 'TEXT';
					break;
			}

			if (array_key_exists('required', $field_def) && $field_def['required'])
				$line .= ' NOT NULL';

			$line .= ',';

			$sql[] = $line;
		}
	}

	/**
	 * @return bool
	 */
	public static function createTable()
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		$multilang = array_key_exists('multilang', $definition) && $definition['multilang'];

		$sql = array();
		$sql[] = 'CREATE';
		$sql[] = 'TABLE IF NOT EXISTS `'._DB_PREFIX_.pSQL($definition['table']).'` (';
		$sql[] = '   `'.pSQL($definition['primary']).'` INT(11) NOT NULL AUTO_INCREMENT,';

		self::iterateFieldsForSql($sql, false);

		$sql[] = '   PRIMARY KEY (`'.pSQL($definition['primary']).'`)';
		$sql[] = ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		$sql = implode(PHP_EOL, $sql);

		if (!$multilang)
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);

		$sql_lang = array();
		$sql_lang[] = 'CREATE';
		$sql_lang[] = 'TABLE IF NOT EXISTS `'._DB_PREFIX_.pSQL($definition['table']).'_lang` (';
		$sql_lang[] = '   `'.psql($definition['primary']).'` INT(11) NOT NULL,';

		self::iterateFieldsForSql($sql_lang, true);

		$sql_lang[] = '   `id_lang` INT(11) NOT NULL';
		$sql_lang[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8';

		$sql_lang = implode(PHP_EOL, $sql_lang);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql) && Db::getInstance()->execute($sql_lang);
	}

	/**
	 * @return bool
	 */
	public static function dropTable()
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');
		$multilang = array_key_exists('multilang', $definition) && $definition['multilang'];
		SYADatabaseTools::dropTable('market_category_aliases');
		return SYADatabaseTools::dropTable($definition['table'])
		&& (!$multilang || SYADatabaseTools::dropTable($definition['table'].'_lang'));
	}

	/**
	 * @param int $id_lang
	 * @param int $p
	 * @param int $n
	 * @param string $order_by
	 * @param string $order_way
	 * @return array
	 * @throws PrestaShopDatabaseException
	 */
	public static function getList($id_lang = null, $p = 1, $n = 10, $order_by = null, $order_way = null)
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		$multilang = array_key_exists('multilang', $definition) && $definition['multilang'];

		if ($p < 1) $p = 1;

		$sql = array();
		$sql[] = 'SELECT a.*';
		if ($multilang)
			$sql[] = ', al.*';

		SYATools::staticCall(get_called_class(), 'extendListSelect', array(&$sql));

		$sql[] = 'FROM `'._DB_PREFIX_.pSQL($definition['table']).'` a';

		if ($multilang)
		{
			if (null === $id_lang)
				$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

			$sql[] = 'LEFT JOIN `'._DB_PREFIX_.pSQL($definition['table']).'_lang` al ON';
			$sql[] = 'a.'.pSQL($definition['primary']).' = al.'.pSQL($definition['primary']);
			$sql[] = 'AND';
			$sql[] = 'al.id_lang = '.(int)$id_lang;
		}

		SYATools::staticCall(get_called_class(), 'extendListJoin', array(&$sql));

		if (null === $order_by)
			$order_by = $definition['primary'];

		if (null === $order_way)
			$order_way = 'DESC';

		$order_way = (string)Tools::strtoupper($order_way);
		$order_by = (string)Tools::strtolower($order_by);

		if (!in_array($order_way, array('ASC', 'DESC'), true))
			$order_way = 'DESC';

		if ($definition['primary'] !== $order_way && !array_key_exists($order_by, $definition['fields']))
			$order_by = $definition['primary'];

		$sql[] = 'WHERE 1';

		SYATools::staticCall(get_called_class(), 'extendListWhere', array(&$sql));

		$sql[] = 'ORDER BY a.`'.pSQL($order_by).'` '.pSQL($order_way);
		$sql[] = 'LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

		$sql = implode(PHP_EOL, $sql);

		return Db::getInstance()->executeS($sql);
	}

	/**
	 * @param array &$sql
	 * @return array
	 */
	protected static function extendListSelect(array &$sql)
	{
		return $sql; // Do nothing
	}

	/**
	 * @param array &$sql
	 * @return array
	 */
	protected static function extendListJoin(array &$sql)
	{
		return $sql; // Do nothing
	}

	/**
	 * @param array &$sql
	 * @return array
	 */
	protected static function extendListWhere(array &$sql)
	{
		return $sql; // Do nothing
	}

	/**
	 * @return int
	 */
	public static function getTotalObjectsCount()
	{
		$definition = SYATools::staticGet(get_called_class(), 'definition');

		$sql = 'SELECT COUNT(*) as count FROM `'._DB_PREFIX_.pSQL($definition['table']).'`';

		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function l($string)
	{
		return Translate::getModuleTranslation(
			SYATools::getModuleNameFromPath(__FILE__),
			$string,
			get_class($this)
		);
	}

	/**
	 * @return bool
	 */
	public function noop()
	{
		/** @noinspection PhpExpressionResultUnusedInspection */
		42;
	}

	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @param int $id_lang
	 * @return Collection|PrestaShopCollection
	 */
	public static function collection($id_lang = null)
	{
		return self::collectionByClass(get_called_class(), $id_lang);
	}

	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @param $class
	 * @param int $id_lang
	 * @return Collection|PrestaShopCollection
	 */
	public static function collectionByClass($class, $id_lang = null)
	{
		if (class_exists('PrestaShopCollection'))
			return new PrestaShopCollection($class, $id_lang);

		/** @noinspection PhpUndefinedClassInspection */
		return new Collection($class, $id_lang);
	}

	/**
	 * @param int $id_lang
	 * @return array
	 */
	public static function getAll($id_lang = null)
	{
		$collection = self::collectionByClass(get_called_class(), $id_lang);
		return $collection->getResults();
	}

	/**
	 * @param ObjectModel $object
	 * @return bool|string
	 */
	public static function humanValidate(ObjectModel $object)
	{
		$def = SYATools::staticGet(get_class($object), 'definition');
		$multilang = array_key_exists('multilang', $def) && $def['multilang'];

		$error = self::humanValidateFields($object);

		if ($multilang && true === $error)
			$error = self::humanValidateFieldsLang($object);

		return $error;
	}

	/**
	 * @param ObjectModel $object
	 * @return bool|string
	 * @throws PrestaShopException
	 */
	protected static function humanValidateFields(ObjectModel $object)
	{
		$definition = SYATools::staticGet(get_class($object), 'definition');
		$update_fields = SYATools::getProtectedPropertyValue($object, 'update_fields');

		foreach ($definition['fields'] as $field => $data)
		{
			if (!empty($data['lang']))
				continue;

			if (is_array($update_fields) && empty($update_fields[$field]))
				continue;

			$message = $object->validateField($field, $object->$field, null, array(), true);
			if ($message !== true)
				return $message;
		}

		return true;
	}

	/**
	 * @param ObjectModel $object
	 * @return bool|string
	 * @throws PrestaShopException
	 */
	protected static function humanValidateFieldsLang(ObjectModel $object)
	{
		$definition = SYATools::staticGet(get_class($object), 'definition');
		$update_fields = SYATools::getProtectedPropertyValue($object, 'update_fields');

		foreach ($definition['fields'] as $field => $data)
		{
			if (empty($data['lang']))
				continue;

			$values = $object->$field;

			// If the object has not been loaded in multilanguage, then the value is the one for the current language of the object
			if (!is_array($values))
				$values = array($object->id_lang => $values);

			// The value for the default must always be set, so we put an empty string if it does not exists
			if (!array_key_exists(Configuration::get('PS_LANG_DEFAULT'), $values))
				$values[Configuration::get('PS_LANG_DEFAULT')] = '';

			foreach ($values as $id_lang => $value)
			{
				if (is_array($update_fields) && empty($update_fields[$field][$id_lang]))
					continue;

				$message = $object->validateField($field, $value, $id_lang, array(), true);
				if ($message !== true)
					return $message;
			}
		}

		return true;
	}
}