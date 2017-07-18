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
 * Class SYAConfigurationTools
 */
abstract class SYAConfigurationTools
{
	/**
	 * @param $text
	 * @param string $prefix
	 * @return bool|string
	 */
	public static function toConfigurationName($text, $prefix = 'SYA_')
	{
		$prefix_len = Tools::strlen($prefix);
		$max_len = 32 - $prefix_len;

		if (is_array($text))
			$text = implode('_', $text);

		if (SYATools::isPs15() && Tools::strlen($text) > $max_len)
			$text = Tools::substr(md5($text), 0, $max_len);

		return Tools::strtoupper($prefix.$text);
	}

	/**
	 * @param $name
	 * @return string
	 */
	public static function get($name)
	{
		return Configuration::get(self::toConfigurationName($name));
	}

	/**
	 * @param $name
	 * @param $value
	 * @param bool $html
	 * @return bool
	 */
	public static function update($name, $value, $html = false)
	{
		return Configuration::updateValue(self::toConfigurationName($name), $value, $html);
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public static function delete($name)
	{
		return Configuration::deleteByName(self::toConfigurationName($name));
	}
}