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
 * Class SYAJSONRequest
 */
class SYAJSONRequest
{
	/**
	 * @var $instance
	 */
	private static $instance;

	protected $data = array();

	/**
	 * SYAJSONRequest constructor.
	 */
	private function __construct()
	{
		$this->data = $this->getRequestData();
		if (!is_array($this->data))
			throw new LogicException('Invalid json');
	}

	private function getRequestData()
	{
		$request = ${'_REQUEST'};
		if (array_key_exists('json_request', $request))
			return Tools::jsonDecode($request['json_request'], true);

		return array();
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function isSubmitted($name)
	{
		return array_key_exists($name, $this->data);
	}

	/**
	 * @param $name
	 * @param null $default
	 * @return null
	 */
	public function get($name, $default = null)
	{
		return $this->isSubmitted($name) ? $this->data[$name] : $default;

	}


	/**
	 * @param $name
	 * @param null $default
	 * @return null
	 */
	public static function getValue($name, $default = null)
	{
		return self::getInstance()->get($name, $default);

	}

	/**
	 * @param $name
	 * @return bool
	 */
	public static function isSubmit($name)
	{
		return self::getInstance()->isSubmitted($name);

	}

	/**
	 * @throws Exception
	 */
	private function __wakeup()
	{
		throw new Exception();
	}

	/**
	 * @throws Exception
	 */
	private function __clone()
	{
		throw new Exception();
	}

	/**
	 * @return SYAJSONRequest
	 */
	public static function getInstance()
	{
		if (null === self::$instance)
			self::$instance = new self();

		return self::$instance;
	}

}