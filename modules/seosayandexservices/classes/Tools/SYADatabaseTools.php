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
 * Class SYADatabaseTools
 */
abstract class SYADatabaseTools
{
	/**
	 * @param string $table
	 * @param bool $safe
	 * @return bool
	 */
	public static function dropTable($table, $safe = true)
	{
		$drop_text = 'DROP';
		$table_text = 'TABLE';
		return (bool)Db::getInstance()->execute(
			$drop_text.' '.$table_text.' '.($safe ? 'IF EXISTS': '').' `'._DB_PREFIX_.pSQL($table).'`'
		);
	}

	/**
	 * @param string $table
	 * @param string $key_name
	 *
	 * @return bool
	 */
	public static function hasIndex($table, $key_name)
	{
		return (bool)Db::getInstance()->executeS(
			'SHOW INDEX FROM `'._DB_PREFIX_.pSQL($table).'` WHERE `Key_name` = "'.pSQL($key_name).'"'
		);
	}

	/**
	 * @param string $table
	 * @param string $key_name
	 *
	 * @return bool
	 */
	public static function dropIndex($table, $key_name)
	{
		return (bool)Db::getInstance()->execute(
			'ALTER TABLE `'._DB_PREFIX_.pSQL($table).'` DROP INDEX `'.pSQL($key_name).'`'
		);
	}

	/**
	 * @param string $table
	 * @param string $key_name
	 *
	 * @return bool
	 */
	public static function dropIndexIfExists($table, $key_name)
	{
		if (self::hasIndex($table, $key_name))
			return self::dropIndex($table, $key_name);

		return true;
	}

	/**
	 * @param string $table
	 * @return bool
	 */
	public static function hasPrimaryKey($table)
	{
		return self::hasIndex($table, 'PRIMARY');
	}

	/**
	 * @param string $table
	 * @return bool
	 */
	public static function dropPrimaryKey($table)
	{
		return (bool)Db::getInstance()->execute(
			'ALTER TABLE `'._DB_PREFIX_.pSQL($table).'` DROP PRIMARY KEY'
		);
	}

	/**
	 * @param string $table
	 * @return bool
	 */
	public static function dropPrimaryKeyIfExists($table)
	{
		if (self::hasPrimaryKey($table))
			return self::dropPrimarykey($table);

		return true;
	}

	/**
	 * @param string $table
	 * @param string|array $columns
	 * @param null|string $key_name
	 *
	 * @return bool
	 */
	public static function addIndex($table, $columns, $key_name = null)
	{
		if (!is_array($columns))
			$columns = array($columns);

		foreach ($columns as &$column)
			$column = '`'.pSQL($column).'`';

		$sql = 'ALTER';
		$sql .= ' TABLE `'._DB_PREFIX_.pSQL($table).'`';
		$sql .= ' ADD INDEX';
		if ($key_name)
			$sql .= ' `'.pSQL($key_name).'`';

		$sql .= ' ('.implode(',', $columns).')';

		return (bool)Db::getInstance()->execute($sql);
	}

	/**
	 * @param string $table
	 * @param string|array $columns
	 * @param string $key_name
	 *
	 * @return bool
	 */
	public static function addIndexIfNotExists($table, $columns, $key_name)
	{
		if (!self::hasIndex($table, $key_name))
			return self::addIndex($table, $columns, $key_name);

		return true;
	}

	/**
	 * @param string $table
	 * @param string|array $columns
	 * @param string $comment
	 *
	 * @return bool
	 */
	public static function addPrimaryKey($table, $columns, $comment = '')
	{
		if (!is_array($columns))
			$columns = array($columns);

		foreach ($columns as &$column)
			$column = '`'.pSQL($column).'`';

		$sql = 'ALTER';
		$sql .= ' TABLE `'._DB_PREFIX_.pSQL($table).'`';
		$sql .= ' ADD PRIMARY KEY';

		$sql .= ' ('.implode(', ', $columns).')';
		if ($comment)
			$sql .= ' COMMENT "'.pSQL($comment).'"';

		return (bool)Db::getInstance()->execute($sql);
	}

	/**
	 * @param string $table
	 * @return array
	 */
	public static function getColumns($table)
	{
		return Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.$table.'`', true, false);
	}

	/**
	 * @param string $table
	 * @param string $column
	 * @return bool
	 */
	public static function columnExists($table, $column)
	{
		return (bool)Db::getInstance()->executeS(
				'SHOW COLUMNS FROM `'._DB_PREFIX_.$table.'` WHERE `Field` = \''.pSQL($column).'\'',
				true,
				false
		);
	}

	/**
	 * @param string $table
	 * @param string $column
	 * @param string $type
	 * @param bool|false $null
	 * @param null|string $default
	 * @param null|string $after
	 * @return bool
	 */
	public static function createColumn($table, $column, $type, $null = false, $default = null, $after = null)
	{
		$sql = 'ALTER';
		$sql .= ' TABLE `'._DB_PREFIX_.pSQL($table).'` ADD `'.pSQL($column).'` '.pSQL($type);
		if (false === $null)
			$sql .= ' NOT NULL';

		if ($default !== null)
			$sql .= ' DEFAULT "'.pSQL($default).'"';

		if ($after)
			$sql .= ' AFTER `'.pSQL($after).'`';

		return (bool)Db::getInstance()->execute($sql, false);
	}

	/**
	 * @param string $table
	 * @param string $column
	 * @param string $type
	 * @param bool|false $null
	 * @param null|string $default
	 * @param null|string $after
	 * @return bool
	 */
	public static function createColumnIfNotExists($table, $column, $type, $null = false, $default = null, $after = null)
	{
		if (!self::columnExists($table, $column))
			return self::createColumn(
				$table,
				$column,
				$type,
				$null,
				$default,
				$after
			);

		return true;
	}

	/**
	 * @param string $table
	 * @param string $column
	 * @return bool
	 */
	public static function dropColumn($table, $column)
	{
		$sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table).'` DROP `'.pSQL($column).'`';

		return (bool)Db::getInstance()->execute($sql);
	}

	/**
	 * @param string $table
	 * @param string $column
	 * @return bool
	 */
	public static function dropColumnIfExists($table, $column)
	{
		if (self::columnExists($table, $column))
			return self::dropColumn($table, $column);

		return true;
	}
}