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
 * Class SYATranslate
 */
class SYATranslate extends SYAComponent
{
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displayBackOfficeHeader')
		&& $this->registerHook('displayHeader');
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
			'TRANSLATE_API_KEY' => '',
			'TRANSLATE_FRONT_END_TRANSLATE_MODE' => 'plain',
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
			'api_key' => self::getApiKey(),
			'front_end_translation_mode' => self::getFrontEndTranslateMode(),
		);
	}

	/**
	 * @return string
	 */
	public static function getApiKey()
	{
		return SYAConfigurationTools::get('TRANSLATE_API_KEY');
	}

	/**
	 * @return string
	 */
	public static function getFrontEndTranslateMode()
	{
		return SYAConfigurationTools::get('TRANSLATE_FRONT_END_TRANSLATE_MODE');
	}

	/**
	 * @return string
	 */
	public function hookDisplayBackOfficeHeader()
	{
		$this->module->addJS('admin/components/translate/translate.js');
		$this->module->addCSS('admin/components/translate.css');

		$api = sprintf(
				'%s&ajax=true&action=translate&component=%s',
				$this->context->link->getAdminLink('AdminSYAServices', true),
				$this->getName()
		);

		SeoSAYandexServices::registerSmartyFunctions();

		$this->context->smarty->assign(
			array(
				'YANDEX_TRANSLATE_API' => $api
			)
		);

		return $this->render($this->getAdminTemplatePath('initializer.tpl'));
	}

	/**
	 *
	 */
	public function hookDisplayHeader()
	{
		$this->module->addJS('front/components/translate/translate.js');
		$this->module->addCSS('front/components/translate.css');

		$api = $this->context->link->getModuleLink(
				$this->module->name,
				'front',
				array(
					'component' => $this->getName(),
					'component_controller' => 'api',
				)
		);

		SeoSAYandexServices::registerSmartyFunctions();

		$this->context->smarty->assign(
			array(
				'YANDEX_TRANSLATE_API' => $api,
				'YANDEX_TRANSLATE_USER_LANGUAGE' => self::getBestUserLanguages(),
				'YANDEX_TRANSLATE_SHOP_LANGUAGE' => $this->context->language->iso_code,
				'YANDEX_TRANSLATE_MODE' => self::getFrontEndTranslateMode(),
			)
		);

		return $this->render($this->getFrontTemplatePath('initializer.tpl'));
	}

	/**
	 * @param $text
	 * @param $lang
	 * @param $type
	 * @return bool|mixed
	 */
	public static function translate($text, $lang, $type)
	{
		$url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
		$url .= '?key='.SYATranslate::getApiKey();
		if (!is_array($text))
			$text = array($text);

		foreach ($text as $line)
			$url .= '&text='.urlencode($line);

		$url .= '&lang='.$lang;
		$url .= '&type='.$type;

		return Tools::file_get_contents($url);
	}

	/**
	 * @void
	 */
	public static function ajaxProcessTranslate()
	{
		header('Content-type:application/json');
		print SYATranslate::translate(
				Tools::getValue('text'),
				Tools::getValue('lang'),
				Tools::getValue('type')
		);
		exit;
	}

	/**
	 * @return string
	 */
	public static function getBestUserLanguages()
	{
		$server = &${'_SERVER'};
		$accept = array_key_exists('HTTP_ACCEPT_LANGUAGE', $server) ? $server['HTTP_ACCEPT_LANGUAGE'] : '';

		$accept = explode(',', $accept);

		$best = array(
			'lang' => 'en',
			'rate' => 0
		);

		foreach ($accept as $lang)
		{
			if (!$lang)
				continue;

			$lang = explode(';', $lang);
			$count = count($lang);
			if ($lang[0] && $count)
			{
				if (strpos($lang[0], '-') !== false)
					continue;

				if ($count === 1)
					$lang[1] = 1.0;
				else
					$lang[1] = (float)str_replace('q=', '', $lang[1]);

				if ($lang[1] > $best['rate'])
				{
					$best['rate'] = $lang[1];
					$best['lang'] = $lang[0];
				}
			}
		}

		return $best['lang'];
	}
}