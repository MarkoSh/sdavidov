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
 * Class SYAPartner
 */
class SYAPartner extends SYAComponent
{
	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displayHome')
		&& $this->registerHook('displayRightColumn')
		&& $this->registerHook('displayLeftColumn')
		&& $this->registerHook('displayFooter')
		&& $this->registerHook('displayLeftColumnProduct')
		&& $this->registerHook('displayRightColumnProduct')
		&& $this->registerHook('displayFooterProduct');
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
			'PARTNER_DISPLAY_HOME' => '',
			'PARTNER_DISPLAY_RIGHT_COLUMN' => '',
			'PARTNER_DISPLAY_LEFT_COLUMN' => '',
			'PARTNER_DISPLAY_FOOTER' => '',
			'DISPLAY_LEFT_COLUMN_PRODUCT' => '',
			'PARTNER_DISPLAY_RIGHT_COLUMN_PRODUCT' => '',
			'PARTNER_DISPLAY_FOOTER_PRODUCT' => '',
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
			'PARTNER_DISPLAY_HOME' => (string)SYAConfigurationTools::get('PARTNER_DISPLAY_HOME'),
			'PARTNER_DISPLAY_RIGHT_COLUMN' => (string)SYAConfigurationTools::get('PARTNER_DISPLAY_RIGHT_COLUMN'),
			'PARTNER_DISPLAY_LEFT_COLUMN' => (string)SYAConfigurationTools::get('PARTNER_DISPLAY_LEFT_COLUMN'),
			'PARTNER_DISPLAY_FOOTER' => (string)SYAConfigurationTools::get('PARTNER_DISPLAY_FOOTER'),
			'PARTNER_DISPLAY_LEFT_COLUMN_PRODUCT' => (string)SYAConfigurationTools::get('PARTNER_DISPLAY_LEFT_COLUMN_PRODUCT'),
			'PARTNER_DISPLAY_RIGHT_COLUMN_PRODUCT' => (string)SYAConfigurationTools::get('PARTNER_DISPLAY_RIGHT_COLUMN_PRODUCT'),
			'PARTNER_DISPLAY_FOOTER_PRODUCT' => (string)SYAConfigurationTools::get('PARTNER_DISPLAY_FOOTER_PRODUCT'),
		);
	}

	/**
	 * @param $conf
	 * @return string
	 */
	public function renderHook($conf)
	{
		return (string)SYAConfigurationTools::get('PARTNER_'.$conf);
	}

	/**
	 * @return string
	 */
	public function hookDisplayHome()
	{
		return $this->renderHook('DISPLAY_HOME');
	}

	/**
	 * @return string
	 */
	public function hookDisplayRightColumn()
	{
		return $this->renderHook('DISPLAY_RIGHT_COLUMN');
	}

	/**
	 * @return string
	 */
	public function hookDisplayLeftColumn()
	{
		return $this->renderHook('DISPLAY_LEFT_COLUMN');
	}

	/**
	 * @return string
	 */
	public function hookDisplayRightColumnProduct()
	{
		return $this->renderHook('DISPLAY_RIGHT_COLUMN_PRODUCT');
	}

	/**
	 * @return string
	 */
	public function hookDisplayLeftColumnProduct()
	{
		return $this->renderHook('DISPLAY_LEFT_COLUMN_PRODUCT');
	}

	/**
	 * @return string
	 */
	public function hookDisplayFooterProduct()
	{
		return $this->renderHook('DISPLAY_FOOTER_PRODUCT');
	}

	/**
	 * @return string
	 */
	public function hookDisplayFooter()
	{
		return $this->renderHook('DISPLAY_FOOTER');
	}
}