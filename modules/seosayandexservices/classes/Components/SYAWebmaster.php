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
 * Class SYAWebmaster
 */
class SYAWebmaster extends SYAComponent
{

	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displayHeader');
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
				'WEBMASTER_META_TAG' => '',
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
				'meta_tag' => self::getMetaTagValue()
		);
	}

	/**
	 * @return string
	 */
	public function hookDisplayHeader()
	{
		return self::getMetaTagValue();
	}

	/**
	 * @return string
	 */
	protected static function getMetaTagValue()
	{
		return htmlspecialchars_decode(
			(string)SYAConfigurationTools::get('WEBMASTER_META_TAG')
		);
	}
}