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
 * Class SYATools
 */
class SYATools
{
	/**
	 * @void noop
	 */
	public static function noop()
	{
		/** @noinspection PhpExpressionResultUnusedInspection */
		42;
	}

	/**
	 * @param mixed $mixed
	 * @return mixed
	 */
	public static function returnValue($mixed)
	{
		return $mixed;
	}

	/**
	 * @return bool
	 */
	public static function isPs15()
	{
		return self::isPsLower('1.6') && !self::isPsLower('1.5');
	}

	/**
	 * @param string $version
	 * @return bool
	 */
	public static function isPsGreater($version)
	{
		return version_compare(_PS_VERSION_, $version, '>');
	}

	/**
	 * @param string $version
	 * @return bool
	 */
	public static function isPsLower($version)
	{
		return version_compare(_PS_VERSION_, $version, '<');
	}

	/**
	 * @param string $pattern
	 * @param int $flags
	 * @return array
	 */
	public static function globRecursive($pattern, $flags = 0)
	{
		$files = glob($pattern, $flags);
		if (!$files)
			$files = array();

		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$files = array_merge($files, self::globRecursive($dir.'/'.basename($pattern), $flags));

		return $files;
	}

	/**
	 * @param object|string $class
	 * @param string $property
	 * @return mixed
	 */
	public static function staticGet($class, $property)
	{
		if (is_object($class))
			$class = get_class($class);

		$cache_id = __CLASS__.__METHOD__.$class.$property;

		if (!Cache::isStored($cache_id))
		{
			$reflection = new ReflectionClass($class);
			$value = $reflection->getStaticPropertyValue($property);

			Cache::store($cache_id, $value);
			return $value;
		}

		return Cache::retrieve($cache_id);
	}

	/**
	 * @param object $object
	 * @param string $property
	 * @return mixed
	 */
	public static function getProtectedPropertyValue($object, $property)
	{
		$reflection = new ReflectionClass($object);
		/** @noinspection CallableParameterUseCaseInTypeContextInspection */
		$property = $reflection->getProperty($property);

		$is_accessible = $property->isPublic();
		$property->setAccessible(true);
		$value = $property->getValue($object);
		$property->setAccessible($is_accessible);

		return $value;
	}

	/**
	 * @param string|object $class
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public static function staticCall($class, $method, array $args = array())
	{
		if (is_object($class))
			$class = get_class($class);

		return call_user_func_array(array($class, $method), $args);
	}

	/**
	 * @param Module $module
	 * @param string $param
	 */
	public static function moduleBackOfficeAjaxHook(Module $module, $param = null)
	{
		if (null === $param)
			$param = $module->name.'_ajax';

		if (Tools::isSubmit($param))
		{
			$method = 'ajaxProcess'.Tools::toCamelCase(Tools::getValue($param), true);
			if (method_exists($module, $method))
				try
				{
					self::jsonResponse(call_user_func(array($module, $method)));
				}
				catch (Exception $e)
				{
					self::jsonErrorResponse($e->getMessage());
				}

			self::jsonErrorResponse('Unknown method');
			exit;
		}
	}

	/**
	 * @param mixed $data
	 * @param int $status
	 * @void exit
	 */
	public static function jsonResponse($data, $status = 200)
	{
		echo Tools::jsonEncode(
			array(
				'status' => $status,
				'data' => $data
			)
		);
		exit;
	}

	/**
	 * @param array|string $data
	 * @void exit
	 *
	 */
	public static function jsonErrorResponse($data)
	{
		if (is_string($data))
			$data = array('log' => array(array('level' => 'error', 'message' => $data)));

		self::jsonResponse($data, 500);
	}

	/**
	 * @param string $dir
	 * @param string $base
	 * @param callable $sort_function
	 */
	public static function addJSDirectory($dir, $base, $sort_function = null)
	{
		$context = Context::getContext();
		$files = self::globRecursive($dir.'/**.js');
		if ($sort_function)
			usort($files, $sort_function);

		foreach ($files as $js_file)
		{
			$js_file = str_replace($dir, $base, $js_file);
			$context->controller->addJS($js_file);
		}
	}

	/**
	 * @param string $dir
	 * @param string $base
	 */
	public static function addCSSDirectory($dir, $base)
	{
		$context = Context::getContext();
		foreach (self::globRecursive($dir.'/**.css') as $css_file)
		{
			$css_file = str_replace($dir, $base, $css_file);
			$context->controller->addCSS($css_file);
		}
	}

	/**
	 * @param Module $module
	 * @param string $dir
	 */
	public static function addModuleCSSDirectory(Module $module, $dir = '/views/css')
	{
		self::addCSSDirectory($module->getLocalPath().$dir, $module->getPathUri().$dir);
	}

	/**
	 * @param Module $module
	 * @param string $dir
	 * @param callable $sort_function
	 */
	public static function addModuleJSDirectory(Module $module, $dir = '/views/js', $sort_function = null)
	{
		self::addJSDirectory($module->getLocalPath().$dir, $module->getPathUri().$dir, $sort_function);
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public static function getModuleNameFromPath($path)
	{
		$module_dir = str_replace('\\', '/', _PS_MODULE_DIR_);
		$path = str_replace('\\', '/', $path);
		$path = str_replace($module_dir, '', $path);

		$pos = self::strpos($path, '/');
		if (!$pos)
			return $path;

		return self::substr($path, 0, $pos);
	}

	/**
	 * @param $str
	 * @param $find
	 * @param int $offset
	 * @param string $encoding
	 * @return bool|int|mixed
	 */
	public static function strpos($str, $find, $offset = 0, $encoding = 'UTF-8')
	{
		if (function_exists('mb_strpos'))
			return mb_strpos($str, $find, $offset, $encoding);

		return call_user_func('strpos', $str, $find, $offset);
	}

	/**
	 * @param $str
	 * @param $start
	 * @param bool $length
	 * @param string $encoding
	 * @return bool|string
	 */
	public static function substr($str, $start, $length = false, $encoding = 'utf-8')
	{
		if (is_array($str))
			return false;
		if (function_exists('mb_substr'))
			return mb_substr($str, (int)$start, ($length === false ? Tools::strlen($str) : (int)$length), $encoding);

		return call_user_func('substr', $str, $start, ($length === false ? Tools::strlen($str) : (int)$length));
	}

	/**
	 * @param string $dir
	 * @return bool
	 */
	public static function removeRecursive($dir)
	{
		if (!is_dir($dir) || is_link($dir))
			return unlink($dir);

		foreach (scandir($dir) as $file)
		{
			if ($file === '.' || $file === '..')
				continue;

			if (!self::removeRecursive($dir.DIRECTORY_SEPARATOR.$file))
				return false;
		}

		return rmdir($dir);
	}

	/**
	 * @param mixed $variable
	 * @return string
	 */
	public static function serialize($variable)
	{
		$variable = serialize($variable);
		/** @noinspection SpellCheckingInspection */
		if (function_exists('gzcompress'))
			$variable = gzcompress($variable);
		$variable = call_user_func('base64_encode', $variable);

		return $variable;
	}

	/**
	 * @param string $variable
	 * @return mixed
	 */
	public static function unserialize($variable)
	{
		$variable = call_user_func('base64_decode', $variable);
		/** @noinspection SpellCheckingInspection */
		if (function_exists('gzuncompress'))
			$variable = gzuncompress($variable);
		$variable = unserialize($variable);

		return $variable;
	}

	/**
	 * @param array &$array
	 */
	public static function arrayOfIds(&$array)
	{
		foreach ($array as $key => &$value)
		{
			$value = (int)$value;
			if (!$value)
				unset($array[$key]);
		}
		unset($value);

		/** @noinspection ReferenceMismatchInspection */
		$array = array_unique(array_values($array));
	}

	/**
	 * @void
	 */
	public static function truncateCombinations()
	{
		Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'product_attribute`');
		Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'product_attribute_combination`');
		Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'product_attribute_image`');
		Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'product_attribute_shop`');
	}

	/**
	 * @param Module $module
	 * @param array|string $name
	 * @param $class
	 * @param int $id_parent
	 * @return Tab
	 */
	public static function createTab(Module $module, $name, $class, $id_parent = 0)
	{
		$tab = new Tab();
		$tab->id_parent = $id_parent;
		$tab->class_name = $class;

		$names = array();

		if (!is_array($name))
			$name = array('en' => $name);

		if (!array_key_exists('en', $name))
			$name['en'] = current($name);

		foreach (Language::getLanguages() as $language)
		{
			$id_lang = (int)$language['id_lang'];
			$iso = $language['iso_code'];
			$names[$id_lang] = array_key_exists($iso, $name) ? $name[$iso] : $name['en'];
		}

		$tab->name = $names;
		$tab->module = $module->name;
		$tab->active = true;
		$tab->save();

		return $tab;
	}

	/**
	 * @param $class
	 * @return bool
	 */
	public static function deleteTabByClass($class)
	{
		$id = Tab::getIdFromClassName($class);
		$tab = new Tab($id);

		return $tab->delete();
	}

	/**
	 * @param int $limit
	 */
	public static function setXDebugMaxNestingLevel($limit = 200000)
	{
		try
		{
			ini_set('xdebug.max_nesting_level', (int)$limit);
		}
		catch (Exception $e)
		{
			unset($e);
		}
	}

	/**
	 * @param int $limit
	 */
	public static function setTimeLimit($limit = 0)
	{
		try
		{
			set_time_limit((int)$limit);
		}
		catch (Exception $e)
		{
			unset($e);
		}
	}

	/**
	 * @param string $string
	 * @param bool $ucfirst
	 * @return string
	 */
	public static function underscoreToEn($string, $ucfirst = false)
	{
		$string = str_replace('_', ' ', $string);

		return $ucfirst ? Tools::ucfirst($string) : $string;
	}

	/**
	 * @var int
	 */
	private static $file_upload_max_size = -1;

	/**
	 * @return float|int
	 */
	public static function fileUploadMaxSize()
	{
		if (self::$file_upload_max_size < 0)
		{
			self::$file_upload_max_size = self::parseIniSize(ini_get('post_max_size'));
			$upload_max = self::parseIniSize(ini_get('upload_max_filesize'));
			if ($upload_max > 0 && $upload_max < self::$file_upload_max_size)
				self::$file_upload_max_size = $upload_max;
		}

		return self::$file_upload_max_size;
	}

	/**
	 * @param $size
	 * @return float
	 */
	public static function parseIniSize($size)
	{
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
		if ($unit)
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		else
			return round($size);
	}

	/**
	 * Copy from 1.6 for 1.5
	 * file_exists() wrapper with a call to clearstatcache prior
	 *
	 * @param string $filename File name
	 * @return boolean Cached result of file_exists($filename)
	 */
	public static function fileExistsNoCache($filename)
	{
		clearstatcache();
		return file_exists($filename);
	}

	/**
	 * Copy from 1.6 for 1.5
	 *
	 * @param $source
	 * @param $destination
	 * @param null $stream_context
	 * @return bool|int
	 */
	public static function copy($source, $destination, $stream_context = null)
	{
		$copy = 'copy';

		if (is_null($stream_context) && !preg_match('/^https?:\/\//', $source))
			return $copy($source, $destination);

		return file_put_contents($destination, Tools::file_get_contents($source, false, $stream_context));
	}

	/**
	 * @param mixed $value
	 * @param mixed $type
	 */
	public static function strictType(&$value, $type)
	{
		if (null !== $value)
		{
			switch ($type)
			{
				case 'bool':
				case 'boolean':
					$value = (bool)$value;
					break;
				case 'int':
				case 'integer':
					$value = (int)$value;
					break;
				case 'float':
				case 'double':
					$value = (float)$value;
					break;
				case 'string':
				default:
					$value = (string)$value;
					break;
			}
		}
	}

	public static function getImageLang($smarty)
	{
		$path = $smarty['path'];
		$module_path = SYATools::getModuleNameFromPath(__FILE__).'/views/img/';
		$module_lang_path = $module_path.Context::getContext()->language->iso_code.'/';
		$module_lang_default_path = $module_path.'en/';
		$path_image = false;
		if (file_exists(_PS_MODULE_DIR_.$module_lang_path.$path))
			$path_image = _MODULE_DIR_.$module_lang_path.$path;
		elseif (file_exists(_PS_MODULE_DIR_.$module_lang_default_path.$path))
			$path_image = _MODULE_DIR_.$module_lang_default_path.$path;

		if ($path_image)
			return '<img src="'.$path_image.'">';
		else
			return '[can not load image "'.$path.'"]';
	}

	public static function isSerialized($value, &$result = null)
	{
		if (!is_string($value))
			return false;

		if ($value === 'b:0;')
		{
			$result = false;
			return true;
		}

		$length = Tools::strlen($value);
		$end = '';
		switch ($value[0])
		{
			case 's':
				if ($value[$length - 2] !== '"')
					return false;
			case 'b':
			case 'i':
			case 'd':
				$end .= ';';
			case 'a':
			case 'O':
				$end .= '}';
				if ($value[1] !== ':')
					return false;

				switch ($value[2])
				{
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
						break;
					default:
						return false;
				}
			case 'N':
				$end .= ';';
				if ($value[$length - 1] !== $end[0])
					return false;

				break;
			default:
				return false;
		}

		if (($result = @unserialize($value)) === false)
		{
			$result = null;
			return false;
		}

		return true;
	}

	public static function isJSON($string)
	{
		return ((is_string($string)
			&& (is_object(Tools::jsonDecode($string))
			|| is_array(Tools::jsonDecode($string))))) ? true : false;
	}

	public static function getZipCodeFormat($id_country = null)
	{
		$zip_code_format = false;
		if (!is_null($id_country))
			$zip_code_format = Country::getZipCodeFormat($id_country);
		if (!$zip_code_format)
			$zip_code_format = Country::getZipCodeFormat((int)Configuration::get('PS_COUNTRY_DEFAULT'));
		return $zip_code_format;
	}
}