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
 * Class SYAMaps
 */
class SYAMaps extends SYAComponent
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
			'MAPS_CENTER_LAT' => 55.7519832875364,
			'MAPS_CENTER_LNG' => 37.61715856305493,
			'MAPS_PLACEMARK_STYLE' => 'islands#redStretchyIcon',
			'MAPS_PLACEMARK_CONTENT' => 'We are here!',
			'MAPS_PLACEMARK_LAT' => 55.7519832875364,
			'MAPS_PLACEMARK_LNG' => 37.61715856305493,
			'MAPS_ZOOM' => 15,
			'MAPS_TYPE' => 'yandex#map',
			'MAPS_ZOOM_CONTROL' => true,
			'MAPS_SEARCH_CONTROL' => false,
			'MAPS_RULER_CONTROL' => false,
			'MAPS_TRAFFIC_CONTROL' => false,
			'MAPS_TYPE_SELECTOR' => true,
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
			'center' => array(
				'lat' => (float)SYAConfigurationTools::get('MAPS_CENTER_LAT'),
				'lng' => (float)SYAConfigurationTools::get('MAPS_CENTER_LNG'),
			),
			'placemark' => array(
				'style' => SYAConfigurationTools::get('MAPS_PLACEMARK_STYLE'),
				'content' =>  SYAConfigurationTools::get('MAPS_PLACEMARK_CONTENT'),
				'position' => array(
					'lat' => (float)SYAConfigurationTools::get('MAPS_PLACEMARK_LAT'),
					'lng' => (float)SYAConfigurationTools::get('MAPS_PLACEMARK_LNG'),
				),
			),
			'zoom' => (int)SYAConfigurationTools::get('MAPS_ZOOM'),
			'type' => SYAConfigurationTools::get('MAPS_TYPE'),
			'controls' => array(
				'zoom_control' => (bool)SYAConfigurationTools::get('MAPS_ZOOM_CONTROL'),
				'search_control' => (bool)SYAConfigurationTools::get('MAPS_SEARCH_CONTROL'),
				'ruler_control' => (bool)SYAConfigurationTools::get('MAPS_RULER_CONTROL'),
				'traffic_control' => (bool)SYAConfigurationTools::get('MAPS_TRAFFIC_CONTROL'),
				'type_selector' => (bool)SYAConfigurationTools::get('MAPS_TYPE_SELECTOR'),
			),
		);
	}

	/**
	 * @return string|null
	 */
	public function hookDisplayHeader()
	{
		if (!$this->context->controller instanceof ContactController)
			return null;

		SeoSAYandexServices::registerSmartyFunctions();

		$this->context->smarty->assign('sya_maps_config', self::getConfiguration());
		$this->context->smarty->assign(
			'current_language_iso_code',
			$this->context->language->iso_code
		);
		$this->context->controller->addJS(
			$this->module->getPathUri().'/views/js/front/components/maps/yandex-map.js'
		);

		return $this->render($this->getFrontTemplatePath('initializer.tpl'));
	}
}