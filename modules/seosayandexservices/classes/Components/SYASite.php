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
 * Class SYASite
 */
class SYASite extends SYAComponent
{

	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displayTop');
	}

	/**
	 * @return bool
	 */
	public function enable()
	{
		try
		{
			$position = $this->module->getModulePosition('blocksearch', 'displayTop');
			if (!$position)
				$position = 1;

			$this->module->unregisterModuleHook('blocksearch', 'displayTop');
			$this->module->moveToHookPosition('displayTop', $position);
		}
		catch (Exception $e)
		{
			unset($e);
		}

		return parent::enable();
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
			'SITE_CODE' => '',
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
			'code' => self::getSiteCode()
		);
	}

	/**
	 * @return string
	 */
	protected static function getSiteCode()
	{
		return htmlspecialchars_decode(
			(string)SYAConfigurationTools::get('SITE_CODE')
		);
	}

	/**
	 * @return array
	 */
	public function getAngularValues()
	{
		return array(
			'site_config' => self::getConfiguration(),
		);
	}

	/**
	 * @return string
	 */
	public function hookDisplayTop()
	{
		SeoSAYandexServices::registerSmartyFunctions();

		$this->context->smarty->assign('sya_site_code', $this->getSiteCode());

		return $this->render($this->getFrontTemplatePath('top.tpl'));
	}
}