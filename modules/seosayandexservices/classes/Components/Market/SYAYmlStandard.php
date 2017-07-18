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
 * Class SYAYmlStandard
 */
abstract class SYAYmlStandard
{
	/**
	 * @var array
	 */
	protected static $standard = array(
		'yml_catalog' => array(
			'attributes' => array(
				'date' => array(
					'value' => array(
						'type' => 'date',
						'format' => 'Y-m-d H:m',
						'getter' => 'time',
					),
				)
			),
			'children' => array(
				'shop' => array(
					'children' => array(
						'name' => array(
							'length' => 20,
							'required' => true,
							'value' => array(
								'module_configuration' => 'MARKET_SHOP_NAME',
							),
						),
						'company' => array(
							'value' => array(
								'module_configuration' => 'MARKET_SHOP_NAME',
							),
						),
						'url' => array(
							'required' => true,
							'value' => array(
								'getter' => 'getShopURL'
							),
						),
						'platform' => array(
							'value' => 'PrestaShop'
						),
						'version' => array(
							'value' => _PS_VERSION_,
						),
						'agency' => array(
							'value' => 'SeoSA',
						),
						'email' => array(
							'value' => array(
								'configuration' => 'PS_SHOP_EMAIL',
							),
						),
						'cpa' => array(
							'value' => array(
								'module_configuration' => 'MARKET_CPA',
							)
						),
						'currencies' => array(
							'children' => array(
								'currency' => array(
									'repeat' => 'getCurrencies',
									'attributes' => array(
										'id' => array(
											'value' => array(
												'property' => 'iso_code',
												'required' => true
											)
										),
										'rate' => array(
											'value' => array(
												'property' => 'conversion_rate',
												'type' => 'float',
											)
										),
									),
								)
							),
						),
						'categories' => array(
							'children' => array(
								'category' => array(
									'repeat' => 'getCategories',
									'attributes' => array(
										'id' => array(
											'value' => array(
												'property' => 'id_category',
												'type' => 'int',
												'required' => true,
											)
										),
										'parentId' => array(
											'value' => array(
												'property' => 'id_parent',
												'type' => 'int',
											)
										),
									),
									'value' => array(
										'property' => 'name',
									)
								)
							),
						),
						'offers' => array(
							'children' => array(
								'offer' => array(
									'repeat' => true,
									'attributes' => array(
										'id' => array(
											'no_conf' => true,
											'value' => array(
												'type' => 'string',
												'property' => true
											),
										),
										'type' => array(
											'label' => 'Offer type',
											'default_behaviour' => array(
												'select' => array(
													'none' => 'none',
													'vendor.model' => 'vendor.model',
													'book' => 'book',
													'audiobook' => 'audiobook',
													'artist.title' => 'artist.title',
													'tour' => 'tour',
													'event-ticket' => 'event-ticket',
												),
											),
											'no_override' => true,
											'required' => true,
											'value' => array(
												'type' => 'string',
												'getter' => true
											),
											'info' => 'type='
										),
										'available' => array(
											'label' => 'Available',
											'default_behaviour' => array(
												'select' => array(
													'all' => 'All available',
													'by_quantity' => 'By quantity',
													'none' => 'All preorder',
												),
											),
											'value' => array(
												'type' => 'bool',
												'getter' => true,
											),
											'info' => 'available='
										),
									),
									'children' => array(
										'url' => array(
											'label' => 'URL',
											'no_override' => true,
											'required' => true,
											'value' => array(
												'getter' => true
											),
											'default_behaviour' => array(
												'text' => 'Product URL',
											),
											'info' => 'url'
										),
										'price' => array(
											'label' => 'Price',
											'value' => array(
												'type' => 'int',
												'getter' => true,
											),
											'required' => true,
											'default_behaviour' => array(
												'text' => 'Product price with discounts',
											),
											'info' => 'price'
										),
										'old_price' => array(
											'label' => 'Old Price',
											'node_name' => 'oldprice',
											'allow_exclude' => true,
											'default_behaviour' => array(
												'text' => 'Product price without discounts',
											),
											'value' => array(
												'type' => 'int',
												'getter' => true,
											),
											'info' => 'oldprice'
										),
										'currency_id' => array(
											'label' => 'Currency ID',
											'node_name' => 'currencyId',
											'required' => true,
											'value' => array(
												'getter' => 'getDefaultCurrencyIsoCode'
											),
											'default_behaviour' => array(
												'text' => 'Default currency ISO-code',
											),
											'info' => 'currencyId'
										),
										'category_id' => array(
											'label' => 'Category ID',
											'node_name' => 'categoryId',
											'required' => true,
											'value' => array(
												'property' => 'id_category_default'
											),
											'default_behaviour' => array(
												'text' => 'Default product category',
											),
											'info' => 'categoryId'
										),
										'picture' => array(
											'label' => 'Picture',
											'default_behaviour' => array(
												'text' => 'Product cover',
											),
											'value' => array(
												'getter' => true,
											),
											'allow_exclude' => true,
											'info' => 'picture'
										),
										'store' => array(
											'label' => 'Store',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'type' => 'bool',
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'store'
										),
										'pickup' => array(
											'label' => 'Pickup',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'type' => 'bool',
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'pickup'
										),
										'delivery' => array(
											'label' => 'Delivery',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'type' => 'bool',
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'delivery'
										),
										'local_delivery_cost' => array(
											'label' => 'Local delivery cost',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'type' => 'int',
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'local_delivery_cost'
										),
										'name' => array(
											'label' => 'Name',
											'required' => true,
											'value' => array(
												'property' => true
											),
											'default_behaviour' => array(
												'text' => 'Product name in default language',
											),
											'info' => 'name/model'
										),
										'vendor' => array(
											'label' => 'Vendor',
											'default_behaviour' => array(
												'text' => 'Product manufacturer name',
											),
											'value' => array(
												'getter' => true,
											),
											'allow_exclude' => true,
											'info' => 'vendor'
										),
										'vendorCode' => array(
											'label' => 'Vendor code',
											'default_behaviour' => array(
												'select' => array(
													'ean13' => 'EAN13',
													'upc' => 'UPC',
													'reference' => 'Reference'
												),
											),
											'value' => array(
												'getter' => true,
											),
											'allow_exclude' => true,
											'info' => 'vendorCode'
										),
										'description' => array(
											'label' => 'Description',
											'default_behaviour' => array(
												'select' => array(
													'description' => 'Description',
													'description_short' => 'Description short',
													'features' => 'Features',
												),
											),
											'value' => array(
												'type' => 'text',
												'getter' => true,
											),
											'allow_exclude' => true,
											'info' => 'description'
										),
										'sales_notes' => array(
											'label' => 'Sales notes',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'sales_notes'
										),
										'manufacturer_warranty' => array(
											'label' => 'Manufacturer warranty',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'manufacturer_warranty'
										),
										'adult' => array(
											'label' => 'Adult',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'type' => 'bool',
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'adult'
										),
										'age' => array(
											'label' => 'Age',
											'override_only' => true,
											'default_behaviour' => array(
												'text' => 'Override only',
											),
											'value' => array(
												'property' => true,
											),
											'allow_exclude' => true,
											'info' => 'age'
										),
										'barcode' => array(
											'label' => 'Barcode',
											'default_behaviour' => array(
												'select' => array(
													'ean13' => 'EAN13',
													'upc' => 'UPC',
												),
											),
											'value' => array(
												'getter' => true,
											),
											'allow_exclude' => true,
											'info' => 'barcode'
										),
										'country_of_origin' => array(
											'label' => 'Country of origin',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true
											),
											'allow_exclude' => true,
											'info' => 'country_of_origin'
										),
										'local_delivery_days' => array(
											'label' => 'Local delivery days',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true
											),
											'allow_exclude' => true,
											'info' => 'local_delivery_days'
										),
										'typePrefix' => array(
											'label' => 'Type prefix',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true
											),
											'allow_exclude' => true,
											'info' => 'typePrefix'
										),
										'rec' => array(
											'label' => 'Rec',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true
											),
											'allow_exclude' => true,
											'info' => 'rec'
										),
										'expiry' => array(
											'label' => 'Expiry',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true
											),
											'allow_exclude' => true,
											'info' => 'expiry'
										),
										'weight' => array(
											'label' => 'Weight',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true
											),
											'allow_exclude' => true,
											'info' => 'weight'
										),
										'dimensions' => array(
											'label' => 'Dimensions',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true
											),
											'allow_exclude' => true,
											'info' => 'dimensions'
										),
										'cpa' => array(
											'label' => 'Cpa',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'value' => array(
												'property' => true,
												'type' => 'bool',
												'xml_type' => 'int'
											),
											'allow_exclude' => true,
											'info' => 'cpa'
										),
										'delivery-options' => array(
											'label' => 'Delivery options',
											'default_behaviour' => array(
												'text' => 'Override only'
											),
											'children' => array(
												'option' => array(
													'repeat' => 'getDeliveryOptions',
													'attributes' => array(
														'cost' => array(
															'value' => array(
																'property' => 'cost',
																'required' => true
															)
														),
														'days' => array(
															'value' => array(
																'property' => 'days',
																'required' => true
															)
														),
														'order-before' => array(
															'value' => array(
																'property' => 'order_before'
															),
															'allow_exclude' => true
														)
													)
												)
											),
											'allow_exclude' => true,
											'info' => 'delivery-options'
										),
										'outlets' => array(
											'no_conf' => true,
											'children' => array(
												'outlet' => array(
													'repeat' => 'getOutlets',
													'attributes' => array(
														'id' => array(
															'value' => array(
																'property' => 'id',
																'required' => true
															)
														),
														'instock' => array(
															'value' => array(
																'property' => 'instock',
																'required' => false
															)
														),
														'booking' => array(
															'value' => array(
																'property' => 'booking',
																'required' => false
															)
														)
													)
												)
											)
										),
										'param' => array(
											'no_conf' => true,
											'repeat' => 'getFeaturesAndAttributes',
											'attributes' => array(
												'name' => array(
													'value' => array(
														'property' => 'name',
														'required' => true
													)
												),
												'unit' => array(
													'value' => array(
														'property' => 'unit',
														'required' => false
													)
												),
											),
											'value' => array(
												'property' => 'value',
											),
										),
									),
								)
							),
						)
					),
				),
			)
		)
	);

	/**
	 * @return array
	 */
	public static function getStandard()
	{
		return self::$standard;

	}

	/**
	 * @return array
	 */
	public static function getOfferStandard()
	{
		return self::$standard['yml_catalog']['children']['shop']['children']['offers']['children']['offer'];
	}
}