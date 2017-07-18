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
 * Class SYAShare
 */
class SYAShare extends SYAComponent
{
	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHook('displaySocialSharing')
		&& $this->registerHook('displayHeader')
		&& $this->registerHook('displayRightColumnProduct')
		&& $this->registerHook('extraleft')
		&& $this->registerHook('productActions')
		&& $this->registerHook('productFooter')
		&& $this->registerHook('displayCompareExtraInformation');
	}

	/**
	 * @return array
	 */
	protected function getDefaults()
	{
		return array(
			'SHARE_DISPLAY_ON_PRODUCT_PAGE'=> true,
			'SHARE_DISPLAY_ON_COMPARE_PAGE'=> true,
			'SHARE_DISPLAY_ON_CMS_PAGE'=> true,
			'SHARE_CONFIG' => '{
				"theme": "default",
				"style": "button",
				"border": false,
				"linkUnderline": false,
				"linkIcon": false,
				"copyPasteField": false,
				"main_block": [
					"vkontakte",
					"twitter",
					"facebook"
				],
				"popup_blocks": [
					{
						"title": "",
						"socials": [
							"vkontakte",
							"facebook",
							"twitter",
							"odnoklassniki",
							"moimir",
							"lj"
						]
					}
				]
			}'
		);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		try
		{
			$conf = Tools::jsonDecode(SYAConfigurationTools::get('SHARE_CONFIG'), true);
			if (!is_array($conf))
				$conf = array();
		}
		catch (Exception $e)
		{
			unset($e);
			$conf = array();
		}

		$conf['display_on_product_page'] = (bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_PRODUCT_PAGE');
		$conf['display_on_compare_page'] = (bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_COMPARE_PAGE');
		$conf['display_on_cms_page'] = (bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_CMS_PAGE');

		return $conf;
	}


	/**
	 * @return null|string
	 */
	public function hookDisplayHeader()
	{
		$controller = $this->context->controller;

		$show_compare = (bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_COMPARE_PAGE')
				&& $controller instanceof CompareController;

		$show_product = (bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_PRODUCT_PAGE')
				&& $controller instanceof ProductController;

		$show_cms = (bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_CMS_PAGE')
				&& $controller instanceof CmsController;

		if (!$show_compare && !$show_product && !$show_cms)
			return null;

		SeoSAYandexServices::registerSmartyFunctions();

		$this->context->smarty->assign('sya_share_config', self::getConfiguration());

		$this->context->smarty->assign(
				'current_language_iso_code',
				$this->context->language->iso_code
		);

		$this->context->smarty->assign(
				'current_language_iso_code',
				$this->context->language->iso_code
		);

		$this->context->controller->addJS(
				$this->module->getPathUri().'/views/js/front/components/share/yandex-share.js'
		);

		if ($show_cms && $controller instanceof CmsController && $controller->cms instanceof CMS)
		{
				$this->context->smarty->assign(array(
					'sya_sharing_title' => $controller->cms->meta_title,
					'sya_sharing_description' => $controller->cms->meta_description,
					'sya_sharing_url' =>  $this->context->link->getCMSLink(
							$controller->cms,
							$controller->cms->link_rewrite, $controller->ssl
					),
					'sya_sharing_image' => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'))
				));
				$controller->cms->content .= $this->render($this->getFrontTemplatePath('hook.tpl'));
		}

		return $this->render($this->getFrontTemplatePath('initializer.tpl'));
	}

	/**
	 * @return string
	 * @throws PrestaShopException
	 */
	public function renderProductPageHook()
	{
		if (!(bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_PRODUCT_PAGE'))
			return null;

		$controller = $this->context->controller;
		if (!$controller instanceof ProductController)
			return null;

		SeoSAYandexServices::registerSmartyFunctions();

		$product = $controller->getProduct();
		if (!$product instanceof Product)
			return null;

		$image_cover_id = $product->getCover($product->id);
		if (is_array($image_cover_id) && isset($image_cover_id['id_image']))
			$image_cover_id = (int)$image_cover_id['id_image'];
		else
			$image_cover_id = 0;

		$this->context->smarty->assign(array(
				'sya_sharing_title' => $product->name,
				'sya_sharing_description' => $product->description_short,
				'sya_sharing_url' => $this->context->link->getProductLink($product),
				'sya_sharing_image' => $this->context->link->getImageLink($product->link_rewrite, $image_cover_id)
		));

		return $this->render($this->getFrontTemplatePath('hook.tpl'));
	}

	/**
	 * @return null|string
	 */
	public function hookDisplayCompareExtraInformation()
	{
		if (!(bool)SYAConfigurationTools::get('SHARE_DISPLAY_ON_COMPARE_PAGE'))
			return null;

		SeoSAYandexServices::registerSmartyFunctions();

		$this->context->smarty->assign(array(
			'sya_sharing_title' => $this->l('Product comparison'),
			'sya_sharing_description' => '',
			'sya_sharing_url' => $this->context->link->getPageLink(
					'products-comparison',
					null,
					$this->context->language->id,
					array(
						'compare_product_list' => Tools::getValue('compare_product_list')
					)
			),
			'sya_sharing_image' => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'))
		));

		return $this->render($this->getFrontTemplatePath('hook.tpl'));
	}

	/**
	 * @return string
	 */
	public function hookDisplayRightColumnProduct()
	{
		return $this->renderProductPageHook();
	}

	/**
	 * @return string
	 */
	public function hookExtraleft()
	{
		return $this->renderProductPageHook();
	}

	/**
	 * @return string
	 */
	public function hookProductActions()
	{
		return $this->renderProductPageHook();
	}

	/**
	 * @return string
	 */
	public function hookProductFooter()
	{
		return $this->renderProductPageHook();
	}
}