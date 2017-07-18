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

if (version_compare(PHP_VERSION, '5.3', '<'))
	require_once dirname(__FILE__).'/php5.2.inc.php__';

/**
 * Class SeoSAYandexServices
 */
class SeoSAYandexServices extends PaymentModule
{
	/**
	 * @var bool Autoloader Registered
	 */
	protected static $autoloader_registered = false;

	/**
	 * @var bool
	 */
	protected $angular_app_loaded = false;

	/**
	 * @var SYAComponent[]
	 */
	protected $components;

	/**
	 * SeoSAYandexServices Constructor
	 */
	public function __construct()
	{
		$this->name = 'seosayandexservices';

		$this->tab = 'payments_gateways';
		$this->version = '1.2.1';

		$this->author = 'SeoSA';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->is_configurable = true;

		parent::__construct();

		$this->displayName = $this->l('SeoSA Yandex Services');
		$this->description = $this->l('Integrate your shop with Yandex services');
		$this->module_key = '6785ed5391be90fb8bdaf9c5df8c9127';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return bool
	 */
	public function install()
	{
		self::registerAutoloader();

		return parent::install()
		&& $this->installTables()
		&& $this->installAdminTab()
		&& $this->installComponents();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return bool
	 */
	public function uninstall()
	{
		self::registerAutoloader();

		return $this->dropTables()
		&& $this->uninstallAdminTab()
		&& $this->uninstallComponents()
		&& parent::uninstall();
	}

	/**
	 * @return bool
	 */
	protected function installTables()
	{
		$create_sya_component_hooks = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sya_component_hooks` (
			  `id_hook` INT(11) NOT NULL,
			  `component` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

		$create_sya_component_hooks_indexes = 'ALTER TABLE `'._DB_PREFIX_.'sya_component_hooks`
			ADD UNIQUE KEY `component` (`id_hook`, `component`(32));';

		return Db::getInstance()->execute($create_sya_component_hooks)
		&& Db::getInstance()->execute($create_sya_component_hooks_indexes);
	}

	/**
	 * @return bool
	 */
	protected function dropTables()
	{
		return SYADatabaseTools::dropTable('sya_component_hooks');
	}

	/**
	 * @return Tab
	 */
	protected function installAdminTab()
	{
		return SYATools::createTab(
			$this,
			array(
				'en' =>'Yandex Services',
				'ru' =>'Сервисы Yandex',
				'fr' =>'Services de Yandex',
			),
			'AdminSYAServices',
			Tab::getIdFromClassName((version_compare(_PS_VERSION_, '1.7.0.0', '<') ? 'AdminParentModules' : 'AdminParentModulesSf'))
		);
	}

	/**
	 * @return bool
	 */
	protected function uninstallAdminTab()
	{
		return SYATools::deleteTabByClass('AdminSYAServices');
	}

	/**
	 * @return bool
	 */
	protected function installComponents()
	{
		foreach ($this->getComponents() as $component)
			if (!$component->install()) return false;

		return true;
	}

	/**
	 * @return bool
	 */
	protected function uninstallComponents()
	{
		foreach ($this->getComponents() as $component)
			if (!$component->uninstall()) return false;

		return true;
	}

	/**
	 * @void
	 */
	public static function registerAutoloader()
	{
		if (self::$autoloader_registered)
			return;

		require_once dirname(__FILE__).'/classes/SYAFolderAutoloader.php';
		self::$autoloader_registered = SYAFolderAutoloader::create(dirname(__FILE__).'/classes');
	}

	/**
	 * @void
	 */
	public static function registerSmartyFunctions()
	{
		self::registerAutoloader();

		$smarty = Context::getContext()->smarty;

		if (!array_key_exists('sya_json_encode', $smarty->registered_plugins['modifier']))
			smartyRegisterFunction($smarty, 'modifier', 'sya_json_encode', 'json_encode');

		if (!array_key_exists('sya_json_decode', $smarty->registered_plugins['modifier']))
			smartyRegisterFunction($smarty, 'modifier', 'sya_json_decode', array('Tools', 'jsonDecode'));

		if (!array_key_exists('no_escape', $smarty->registered_plugins['modifier']))
			smartyRegisterFunction($smarty, 'modifier', 'no_escape', array('SYATools', 'returnValue'));

		if (!array_key_exists('get_image_lang', $smarty->registered_plugins['function']))
			smartyRegisterFunction($smarty, 'function', 'get_image_lang', array('SYATools', 'getImageLang'));
	}

	/**
	 * @deprecated
	 * @use self::registerSmartyFunctions();
	 */
	public static function registerSmartyFunction()
	{
		self::registerSmartyFunctions();
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		if (!$this->context->link)
		{
			/* Server Params */
			$ssl = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED'));
			$protocol = $ssl ? 'https://' : 'http://';

			$this->context->link = new Link($protocol, $protocol);
		}

		$url = $this->context->link->getAdminLink('AdminSYAServices');
		Tools::redirectAdmin($url);
	}

	/**
	 * @return SYAComponent[]
	 */
	public function getComponents()
	{
		$this->loadComponents();

		return $this->components;
	}

	/**
	 * @param string $name
	 *
	 * @return SYAComponent
	 */
	public function getComponent($name)
	{
		$this->loadComponents();

		return array_key_exists($name, $this->components) ? $this->components[$name] : null;
	}

	/**
	 * @void
	 */
	protected function loadComponents()
	{
		if (null === $this->components)
		{
			$this->registerAutoloader();

			$files = glob(dirname(__FILE__).'/classes/Components/SYA*.php');
			foreach ($files as $file)
			{
				$class = str_replace('.php', '', basename($file));
				if (class_exists($class))
				{
					$component = new $class($this);
					if ($component instanceof SYAComponent)
						$this->components[$component->getName()] = $component;
				}
			}
		}
	}

	/**
	 * @param string $hook
	 * @return SYAComponent[]
	 */
	public function getComponentsForHook($hook)
	{
		$id_hook = Hook::getIdByName($hook);

		$sql = 'SELECT `component` FROM `'._DB_PREFIX_.'sya_component_hooks` WHERE `id_hook` = '.(int)$id_hook;
		$components = Db::getInstance()->executeS($sql);

		if ($components)
		{
			$this->loadComponents();
			foreach ($components as $key => &$row)
			{
				$component = $this->getComponent($row['component']);

				if ($component && $component->isEnabled())
					$row = $component;
				else
					unset($components[$key]);
			}
		}

		return $components;
	}

	/**
	 * @param SYAComponent $component
	 * @param $hook
	 *
	 * @return bool
	 */
	public function registerComponentHook(SYAComponent $component, $hook)
	{
		$this->registerHook($hook);

		$id_hook = Hook::getIdByName($hook);

		$data = array(
			'component' => pSQL($component->getName()),
			'id_hook' => (int)$id_hook
		);

		return Db::getInstance()->insert('sya_component_hooks', $data, false, false, Db::INSERT_IGNORE);
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($name, $arguments)
	{
		$this->registerAutoloader();

		if (strpos($name, 'hook') === 0)
		{
			$hook = str_replace('hook', '', $name);

			return $this->execComponentsHook($hook, $arguments);
		}

		throw new Exception(
			sprintf('PHP Fatal error:  Call to undefined method %s::%s()', __CLASS__, $name)
		);
	}

	/**
	 * @return string
	 */
	public function hookDisplayAdminProductsExtra()
	{
		$component = Tools::getValue('component');
		$components = $this->getComponents();
		if ($component && array_key_exists($component, $components))
		{
			$component = $components[$component];

			return call_user_func_array(
				array($component, 'hookDisplayAdminProductsExtra'),
				func_get_args()
			);
		}

		return $this->execComponentsHook('displayAdminProductsExtra', func_get_args());
	}

	/**
	 * @param $hook
	 * @param $arguments
	 * @return string
	 */
	protected function execComponentsHook($hook, $arguments)
	{
		$components = self::getComponentsForHook($hook);

		if ($hook != 'paymentOptions')
			$output = '';
		else
			$output = array();
		foreach ($components as $component)
		{
			$method = 'hook'.Tools::ucfirst($hook);
			$callee = array($component, $method);
			if (is_callable($callee))
			{
				$component_output = call_user_func_array($callee, $arguments);
				if ($component_output)
				{
					if ($hook != 'paymentOptions')
						$output .= $component_output;
					else
						$output = array_merge($output, $component_output);
				}
			}
		}

		return $output;
	}

	/**
	 * @void
	 */
	public function loadAngularApp()
	{
		if ($this->angular_app_loaded)
			return;

		$this->angular_app_loaded = true;

		SeoSAYandexServices::registerSmartyFunctions();

		$this->assignAngularData();
		$this->assignAngularFiles();
		$this->addAngularMedia();
	}

	/**
	 * @void
	 */
	protected function assignAngularData()
	{
		$this->context->smarty->assign('module', $this);
		$components = $this->getComponents();
		$this->context->smarty->assign('components', $components);

		$angular_values = array();
		foreach ($components as $component)
			$angular_values = array_merge($angular_values, $component->getAngularValues());

		$this->context->smarty->assign('angular_values', $angular_values);

		$this->context->smarty->assign('ps15', SYATools::isPs15());
		$this->context->smarty->assign('yandex_admin_controller_url',
			$this->context->link->getAdminLink('AdminSYAServices')
		);

		$this->context->smarty->assign('shop_base_url',
			$this->context->shop->getBaseURL()
		);

		$this->context->smarty->assign('current_language_iso_code',
			$this->context->language->iso_code
		);

		$this->context->smarty->assign('site_logo_image_url',
			$this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'))
		);
	}

	/**
	 * @void
	 */
	protected function assignAngularFiles()
	{
		$angular_templates_folder = $this->getLocalPath().'views/templates/admin/angular-templates';
		$angular_templates = SYATools::globRecursive($angular_templates_folder.'/**.tpl');

		foreach ($angular_templates as &$path)
			$path = str_replace($angular_templates_folder.'/', '', $path);
		unset($path);

		$this->context->smarty->assign('angular_templates', $angular_templates);
	}

	/**
	 * @void
	 */
	protected function addAngularMedia()
	{
		$this->addJS('vendor/angular'.(_PS_MODE_DEV_ ? '' : '.min').'.js');

		$this->addJS('vendor/snackbar.min.js');
		$this->addCSS('vendor/snackbar.min.css');

		$this->addJS('vendor/ZeroClipboard.min.js');

		$this->addJS('vendor/jquery.binarytransport.js');
		$this->addCSS('admin-theme.css');

		$this->addCSS('angular/admin.css');
		if (SYATools::isPs15())
			$this->addCSS('angular/admin-1.5.css');

		$this->context->controller->addJqueryUI('ui.draggable');
		$this->context->controller->addJqueryUI('ui.droppable');
		$this->context->controller->addJqueryUI('ui.sortable');

		SYATools::addModuleJSDirectory($this, 'views/js/angular', array($this, 'strlenNatSortAsc'));
	}

	/**
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public function strlenNatSortAsc($a, $b)
	{
		$a = (int)Tools::strlen($a);
		$b = (int)Tools::strlen($b);

		if ($a === $b)
			return strnatcmp($a, $b);

		return $a > $b ? 1 : - 1;
	}

	/**
	 * @see ModuleAdminController::addJS()
	 *
	 * @param $js_uri
	 * @param bool|true $check_path
	 */
	public function addJS($js_uri, $check_path = true)
	{
		if (!is_array($js_uri))
			$js_uri = array($js_uri);

		foreach ($js_uri as &$uri)
			$uri = $this->getPathUri().'views/js/'.$uri;

		$this->context->controller->addJS($js_uri, $check_path);
	}

	/**
	 * @see ModuleAdminController::addCSS()
	 *
	 * @param $css_uri
	 * @param string $css_media_type
	 * @param null $offset
	 * @param bool|true $check_path
	 */
	public function addCSS($css_uri, $css_media_type = 'all', $offset = null, $check_path = true)
	{
		if (!is_array($css_uri))
			$css_uri = array($css_uri);

		foreach ($css_uri as &$uri)
			$uri = $this->getPathUri().'views/css/'.$uri;

		$this->context->controller->addCSS($css_uri, $css_media_type, $offset, $check_path);
	}

	/**
	 * @param string|Module $module
	 * @param string|int $hook_id
	 * @return bool
	 */
	public function unregisterModuleHook($module, $hook_id)
	{
		$id_module = $module instanceof Module ? (int)$module->id : (int)Module::getModuleIdByName($module);
		if (!$id_module)
			return true;

		if (!is_numeric($hook_id))
		{
			$hook_name = (string)$hook_id;
			$hook_id = Hook::getIdByName($hook_name);
			if (!$hook_id)
				return false;
		}

		$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
		WHERE `id_module` = '.(int)$id_module.' AND `id_hook` = '.(int)$hook_id;

		$result = Db::getInstance()->execute($sql);

		$this->cleanPositions($hook_id, null);

		return $result;
	}

	/**
	 * @param $hook_id
	 * @param $position
	 * @return bool
	 */
	public function moveToHookPosition($hook_id, $position)
	{
		if (!is_numeric($hook_id))
		{
			$hook_name = (string)$hook_id;
			$hook_id = Hook::getIdByName($hook_name);
			if (!$hook_id)
				return false;
		}

		$way = (int)$position > (int)$this->getPosition($hook_id);

		return $this->updatePosition($hook_id, $way, $position);
	}

	/**
	 * Return module position for a given hook
	 *
	 * @param string|Module $module
	 * @param int|string $id_hook
	 * @return int position
	 */
	public function getModulePosition($module, $id_hook)
	{
		$id_module = $module instanceof Module ? (int)$module->id : (int)Module::getModuleIdByName($module);

		$preload_modules_from_hooks = 'preloadModulesFromHooks';

		if (isset(Hook::${$preload_modules_from_hooks}))
		{
			if (isset(Hook::${$preload_modules_from_hooks}[$id_hook]))
			{
				if (isset(Hook::${$preload_modules_from_hooks}[$id_hook]['module_position'][$id_module]))
				{
					$return = Hook::${$preload_modules_from_hooks}[$id_hook]['module_position'][$id_module];
					return $return;
				}
				else
					return 0;
			}
		}

		return (int)Db::getInstance()->getValue('
			SELECT `position`
			FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_hook` = '.(int)$id_hook.'
			AND `id_module` = '.(int)$id_module.'
			AND `id_shop` = '.(int)Context::getContext()->shop->id
		);
	}
}