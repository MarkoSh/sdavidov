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
 * Created by IntelliJ IDEA.
 * User: andrew
 * Date: 20.08.15
 * Time: 14:34
 */
class SYAHttpTools
{
	/**
	 * @param $url
	 * @return resource
	 */
	protected static function curl($url)
	{
		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, $url);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		return $handler;
	}

	/**
	 * @param $handler
	 * @return mixed
	 */
	protected static function exec($handler)
	{
		$result = curl_exec($handler);
		curl_close($handler);
		return $result;
	}

	/**
	 * @param $url
	 * @return mixed
	 */
	public static function get($url)
	{
		return self::exec(self::curl($url));
	}

	/**
	 * @param $url
	 * @param $data
	 * @param $headers
	 * @return mixed
	 */
	public static function post($url, $data, $headers = array())
	{
		$handler = self::curl($url);

		if (array_key_exists('Authorization', $headers))
		{
			$token = $headers['Authorization'];
			$headers = array();
			$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
			$headers[] = 'Authorization: '.$token;
		}

		curl_setopt($handler, CURLOPT_POST, 1);
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);

		return self::exec($handler);
	}

	public static function put($url, $data, $headers = array())
	{
		$handler = self::curl($url);
		$headers[] = 'Content-Type: application/json;';
		$body = Tools::jsonEncode($data);
		$fp = tmpfile();
		fwrite($fp, $body, Tools::strlen($body));
		fseek($fp, 0);

		if (array_key_exists('Authorization', $headers))
		{
			$token = $headers['Authorization'];
			$headers = array();
			$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
			$headers[] = 'Authorization: '.$token;
		}

		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($handler, CURLOPT_PUT, true);
		curl_setopt($handler, CURLOPT_INFILE, $fp);
		curl_setopt($handler, CURLOPT_INFILESIZE, Tools::strlen($body));

		return self::exec($handler);
	}
}