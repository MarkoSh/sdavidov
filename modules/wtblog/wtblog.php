<?php
/**
* 2007-2015 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
define('_MODULE_SMARTBLOG_DIR_', _PS_MODULE_DIR_.'wtblog/views/img/');
require_once (dirname(__FILE__).'/classes/BlogCategory.php');
require_once (dirname(__FILE__).'/classes/Blogcomment.php');
require_once (dirname(__FILE__).'/classes/BlogPostCategory.php');
require_once (dirname(__FILE__).'/classes/BlogTag.php');
require_once (dirname(__FILE__).'/classes/WtBlogPost.php');
require_once (dirname(__FILE__).'/classes/BlogImageType.php');
require_once (dirname(__FILE__).'/controllers/admin/AdminAboutUsController.php');
require_once (dirname(__FILE__).'/sql/SampleDataBlog.php');

class WtBlog extends Module
{
	public function __construct()
	{
		$this->name = 'wtblog';
		$this->tab = 'front_office_features';
		$this->version = '1.1.0';
		$this->author = 'waterthemes';
		$this->need_upgrade = true;
		$this->controllers = array('archive', 'category', 'details', 'search', 'tagpost');
		$this->secure_key = Tools::encrypt($this->name);
		$this->wt_shop_id = Context::getContext()->shop->id;
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('WT Blog');
		$this->description = $this->l('The Most Powerfull Presta shop Blog');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
	}
	public function install()
	{
		Configuration::updateGlobalValue('wtpostperpage', '5');
		Configuration::updateGlobalValue('wtshowauthorstyle', '1');
		Configuration::updateGlobalValue('wtmainblogurl', 'wtblog');
		Configuration::updateGlobalValue('wtusehtml', '1');
		Configuration::updateGlobalValue('wtshowauthorstyle', '1');
		Configuration::updateGlobalValue('wtenablecomment', '1');
		Configuration::updateGlobalValue('wtcaptchaoption', '1');
		Configuration::updateGlobalValue('wtshowviewed', '1');
		Configuration::updateGlobalValue('wtshownoimg', '1');
		Configuration::updateGlobalValue('wtshowcolumn', '3');
		Configuration::updateGlobalValue('wtacceptcomment', '1');
		Configuration::updateGlobalValue('wtdisablecatimg', '1');
		Configuration::updateGlobalValue('wtblogmetatitle', 'Wt Bolg Title');
		Configuration::updateGlobalValue('wtblogmetakeyword', 'waterthemes,blog,wtblog,prestashop blog,prestashop,blog');
		Configuration::updateGlobalValue('wtblogmetadescrip', 'Prestashop powerfull blog site developing module.');
		$this->addquickaccess();
		$langs = Language::getLanguages();
		if (!parent::install() || !$this->registerHook('displayHeader') || !$this->SmartHookInsert()
			|| !$this->registerHook('moduleRoutes')
			|| !$this->registerHook('displayBackOfficeHeader') || !$this->registerHook('actionObjectLanguageAddAfter') || !$this->registerHook('actionShopDataDuplication'))
			return false;
		$sql = array();
		require_once(dirname(__FILE__).'/sql/install.php');
		foreach ($sql as $sq)
			if (!Db::getInstance()->Execute($sq))
				return false;
		$this->CreateWtBlogTabs();
		$sample_data = new SampleDataBlog();
		$sample_data->SampleDataInstall();
		return true;
	}
	public function hookdisplayBackOfficeHeader($params)
	{
		$this->smarty->assign(array('wtmodules_dir' => __PS_BASE_URI__));
		return $this->display(__FILE__, 'views/templates/admin/addjs.tpl');
	}
	public function SmartHookInsert()
	{
		$hookvalue = array();
		require_once(dirname(__FILE__).'/sql/addhook.php');
		foreach ($hookvalue as $hkv)
		{
			$hookid = Hook::getIdByName($hkv['name']);
			if (!$hookid)
			{
				$add_hook = new Hook();
				$add_hook->name = pSQL($hkv['name']);
				$add_hook->title = pSQL($hkv['title']);
				$add_hook->description = pSQL($hkv['description']);
				$add_hook->position = pSQL($hkv['position']);
				$add_hook->live_edit  = $hkv['live_edit'];
				$add_hook->add();
				$hookid = $add_hook->id;
				if (!$hookid)
					return false;
			}
			else
			{
				$up_hook = new Hook($hookid);
				$up_hook->update();
			}
		}
		return true;
	}
	public function uninstall()
	{
		if (!parent::uninstall() || !Configuration::deleteByName('wtblogmetatitle') || !Configuration::deleteByName('wtblogmetakeyword') || !Configuration::deleteByName('wtblogmetadescrip') || !Configuration::deleteByName('wtpostperpage') || !Configuration::deleteByName('wtacceptcomment') || !Configuration::deleteByName('wtusehtml') || !Configuration::deleteByName('wtcaptchaoption') || !Configuration::deleteByName('wtshowviewed') || !Configuration::deleteByName('wtdisablecatimg') || !Configuration::deleteByName('wtenablecomment') || !Configuration::deleteByName('wtmainblogurl') || !Configuration::deleteByName('wtshowcolumn') || !Configuration::deleteByName('wtshowauthorstyle') || !Configuration::deleteByName('wtshownoimg') || !Configuration::deleteByName('wtshowauthor'))
			return false;
		$idtabs = array();
		require_once(dirname(__FILE__).'/sql/uninstall_tab.php');
		foreach ($idtabs as $tabid)
			if ($tabid)
			{
				$tab = new Tab($tabid);
				$tab->delete();
			}
		$sql = array();
		require_once(dirname(__FILE__).'/sql/uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;
		$this->SmartHookDelete();
		$this->deletequickaccess();
		return true;
	}
	public function SmartHookDelete()
	{
		$hookvalue = array();
		require_once(dirname(__FILE__).'/sql/addhook.php');
		foreach ($hookvalue as $hkv)
		{
			$hookid = Hook::getIdByName($hkv['name']);
			if ($hookid)
			{
				$dlt_hook = new Hook($hookid);
				$dlt_hook->delete();
			}
		}
	}
	public function hookModuleRoutes($params)
	{
		$alias = Configuration::get('wtmainblogurl');
		$usehtml = (int)Configuration::get('wtusehtml');
		if ($usehtml != 0)
			$html = '.html';
		else
			$html = '';
		$my_link = array(
			'wtblog' => array(
				'controller' => 'category',
				'rule' => $alias.$html,
				'keywords' => array(),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_list' => array(
				'controller' => 'category',
				'rule' => $alias.'/category'.$html,
				'keywords' => array(),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_list_module' => array(
				'controller' => 'category',
				'rule' => 'module/'.$alias.'/category'.$html,
				'keywords' => array(),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_list_pagination' => array(
				'controller' => 'category',
				'rule' =>       $alias.'/category/page/{page}'.$html,
				'keywords' => array(
					'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_pagination' => array(
				'controller' => 'category',
				'rule' =>       $alias.'/page/{page}'.$html,
				'keywords' => array(
					'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_category' => array(
				'controller' => 'category',
				'rule' =>        $alias.'/category/{id_category}_{slug}'.$html,
				'keywords' => array(
					'id_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
					'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_category_pagination' => array(
				'controller' => 'category',
				'rule' =>       $alias.'/category/{id_category}_{slug}/page/{page}'.$html,
				'keywords' => array(
					'id_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
					'page' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
					'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_cat_page_mod' => array(
				'controller' => 'category',
				'rule' =>       'module/'.$alias.'/category/{id_category}_{slug}/page/{page}'.$html,
				'keywords' => array(
					'id_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
					'page' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
					'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_post' => array(
				'controller' => 'details',
				'rule' =>       $alias.'/{id_post}_{slug}'.$html,
				'keywords' => array(
					'id_post' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_post'),
					'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_search' => array(
				'controller' => 'search',
				'rule' => $alias.'/search'.$html,
				'keywords' => array(),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_tag' => array(
				'controller' => 'tagpost',
				'rule' => $alias.'/tag/{tag}'.$html,
				'keywords' => array(
					'tag' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'tag'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_search_pagination' => array(
				'controller' => 'search',
				'rule' =>       $alias.'/search/page/{page}'.$html,
				'keywords' => array(
					'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_archive' => array(
				'controller' => 'archive',
				'rule' => $alias.'/archive'.$html,
				'keywords' => array(),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_archive_pagination' => array(
				'controller' => 'archive',
				'rule' =>       $alias.'/archive/page/{page}'.$html,
				'keywords' => array(
					'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_month' => array(
				'controller' => 'archive',
				'rule' =>       $alias.'/archive/{year}/{month}'.$html,
				'keywords' => array(
					'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
					'month' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_month_pagination' => array(
				'controller' => 'archive',
				'rule' =>       $alias.'/archive/{year}/{month}/page/{page}'.$html,
				'keywords' => array(
					'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
					'month' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
					'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_year' => array(
				'controller' => 'archive',
				'rule' =>       $alias.'/archive/{year}'.$html,
				'keywords' => array(
					'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
			'wtblog_year_pagination' => array(
				'controller' => 'archive',
				'rule' =>       $alias.'/archive/{year}/page/{page}'.$html,
				'keywords' => array(
					'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
					'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => 'wtblog',
				),
			),
		);
		return $my_link;
	}
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/wtblogstyle.css', 'all');
	}
	private function CreateWtBlogTabs()
	{
		$langs = Language::getLanguages();
		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$wttab = new Tab();
		$wttab->class_name = 'AdminWtBlog';
		$wttab->module = '';
		$wttab->id_parent = 0;
		foreach ($langs as $l) $wttab->name[$l['id_lang']] = $this->l('Blog');
		$wttab->save();
		$tab_id = $wttab->id;
		@Tools::copy(dirname(__FILE__).'/views/img/AdminWtBlog.gif', _PS_ROOT_DIR_.'/views/img/t/AdminWtBlog.gif');
		$tabvalue = array();
		require_once(dirname(__FILE__).'/sql/install_tab.php');
		foreach ($tabvalue as $tab)
		{
			$newtab = new Tab();
			$newtab->class_name = $tab['class_name'];
			$newtab->id_parent = $tab_id;
			$newtab->module = $tab['module'];
			foreach ($langs as $l)
				$newtab->name[$l['id_lang']] = $this->l($tab['name']);
			$newtab->save();
		}
		return true;
	}
	public function getContent()
	{
		$html = '';
		if (Tools::isSubmit('savewtblog'))
		{
			Configuration::updateValue('wtblogmetatitle', Tools::getvalue('wtblogmetatitle'));
			Configuration::updateValue('wtenablecomment', Tools::getvalue('wtenablecomment'));
			Configuration::updateValue('wtblogmetakeyword', Tools::getvalue('wtblogmetakeyword'));
			Configuration::updateValue('wtblogmetadescrip', Tools::getvalue('wtblogmetadescrip'));
			Configuration::updateValue('wtpostperpage', Tools::getvalue('wtpostperpage'));
			Configuration::updateValue('wtacceptcomment', Tools::getvalue('wtacceptcomment'));
			Configuration::updateValue('wtcaptchaoption', Tools::getvalue('wtcaptchaoption'));
			Configuration::updateValue('wtshowviewed', Tools::getvalue('wtshowviewed'));
			Configuration::updateValue('wtdisablecatimg', Tools::getvalue('wtdisablecatimg'));
			Configuration::updateValue('wtshowauthorstyle', Tools::getvalue('wtshowauthorstyle'));
			Configuration::updateValue('wtshowauthor', Tools::getvalue('wtshowauthor'));
			Configuration::updateValue('wtshowcolumn', Tools::getvalue('wtshowcolumn'));
			Configuration::updateValue('wtmainblogurl', Tools::getvalue('wtmainblogurl'));
			Configuration::updateValue('wtusehtml', Tools::getvalue('wtusehtml'));
			Configuration::updateValue('wtshownoimg', Tools::getvalue('wtshownoimg'));
			$html = $this->displayConfirmation($this->l('The settings have been updated successfully.'));
			$helper = $this->SettingForm();
			$html .= $helper->generateForm($this->fields_form);
			$helper = $this->regenerateform();
			$html .= $helper->generateForm($this->fields_form);
			return $html;
		}
		elseif (Tools::isSubmit('generateimage'))
		{
			if (Tools::getvalue('isdeleteoldthumblr') != 1)
			{
				BlogImageType::ImageGenerate();
				$html = $this->displayConfirmation($this->l('Generate New Thumblr Succesfully.'));
				$helper = $this->SettingForm();
				$html .= $helper->generateForm($this->fields_form);
				$helper = $this->regenerateform();
				$html .= $helper->generateForm($this->fields_form);
				return $html;
			}
			else
			{
				BlogImageType::ImageDelete();
				BlogImageType::ImageGenerate();
				$html = $this->displayConfirmation($this->l('Delete Old Image and Generate New Thumblr Succesfully.'));
				$helper = $this->SettingForm();
				$html .= $helper->generateForm($this->fields_form);
				$helper = $this->regenerateform();
				$html .= $helper->generateForm($this->fields_form);
				return $html;
			}
		}
		else
		{
			$helper = $this->SettingForm();
			$html .= $helper->generateForm($this->fields_form);
			$helper = $this->regenerateform();
			$html .= $helper->generateForm($this->fields_form);
			return $html;
		}
	}
	public function SettingForm()
	{
		$blog_url = wtblog::GetSmartBlogLink('wtblog');
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->fields_form[0]['form'] = array(
		'legend' => array(
		'title' => $this->l('Setting'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Meta Title'),
					'name' => 'wtblogmetatitle',
					'size' => 70,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta Keyword'),
					'name' => 'wtblogmetakeyword',
					'size' => 70,
					'required' => true
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Meta Description'),
					'name' => 'wtblogmetadescrip',
					'rows' => 7,
					'cols' => 66,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Main Blog Url'),
					'name' => 'wtmainblogurl',
					'size' => 15,
					'required' => true,
					'desc'=> '<p class="alert alert-info"><a href="'.$blog_url.'">'.$blog_url.'</a></p>'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Use .html with Friendly Url'),
					'name' => 'wtusehtml',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
					array(
					'id' => 'wtusehtml',
					'value' => 1,
					'label' => $this->l('Enabled')
					),
					array(
					'id' => 'wtusehtml',
					'value' => 0,
					'label' => $this->l('Disabled')
					)
				)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Number of posts per page'),
					'name' => 'wtpostperpage',
					'size' => 15,
					'required' => true
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Auto accepted comment'),
					'name' => 'wtacceptcomment',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'wtacceptcomment',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'wtacceptcomment',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				),
				array
				(
					'type' => 'radio',
					'label' => $this->l('Enable Captcha'),
					'name' => 'wtcaptchaoption',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'wtcaptchaoption',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'wtcaptchaoption',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Enable Comment'),
					'name' => 'wtenablecomment',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'wtenablecomment',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'wtenablecomment',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Show Author Name'),
					'name' => 'wtshowauthor',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'wtshowauthor',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'wtshowauthor',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				),array(
					'type' => 'radio',
					'label' => $this->l('Show Post Viewed'),
					'name' => 'wtshowviewed',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'wtshowviewed',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'wtshowviewed',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Show Author Name Style'),
					'name' => 'wtshowauthorstyle',
					'required' => false,
					'class' => 't',
					'values' => array(
						array(
						'id' => 'wtshowauthorstyle',
						'value' => 1,
						'label' => $this->l('First Name, Last Name')
						),
						array(
						'id' => 'wtshowauthorstyle',
						'value' => 0,
						'label' => $this->l('Last Name, First Name')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Show No Image'),
					'name' => 'wtshownoimg',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'wtshownoimg',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'wtshownoimg',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				),
				array(
						'type' => 'radio',
						'label' => $this->l('Show Category'),
						'name' => 'wtdisablecatimg',
						'required' => false,
						'class' => 't',
						'desc'=>'Show category image and description on category page',
						'is_bool' => true,
						'values' => array(
						array(
						'id' => 'wtdisablecatimg',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'wtdisablecatimg',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
						)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Blog Page Column Setting'),
					'name' => 'wtshowcolumn',
					'required' => false,
					'class' => 't',
					'values' => array(
					array(
						'id' => 'wtshowcolumn',
						'value' => 0,
						'label' => $this->l('Use Both WtBlog Column')
						),
						array(
						'id' => 'wtshowcolumn',
						'value' => 1,
						'label' => $this->l('Use Only WtBlog Left Column')
						),
						array(
						'id' => 'wtshowcolumn',
						'value' => 2,
						'label' => $this->l('Use Only WtBlog Right Column')
						),
						array(
						'id' => 'wtshowcolumn',
						'value' => 3,
						'label' => $this->l('Use Prestashop Column')
						)
					)
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
					'id_lang' => $lang['id_lang'],
					'iso_code' => $lang['iso_code'],
					'name' => $lang['name'],
					'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'token='.Tools::getAdminTokenLite('AdminModules'),
			)
		);
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;
		$helper->toolbar_scroll = true;
		$helper->submit_action = 'save'.$this->name;
		$helper->fields_value['wtpostperpage'] = Configuration::get('wtpostperpage');
		$helper->fields_value['wtacceptcomment'] = Configuration::get('wtacceptcomment');
		$helper->fields_value['wtshowauthorstyle'] = Configuration::get('wtshowauthorstyle');
		$helper->fields_value['wtshowauthor'] = Configuration::get('wtshowauthor');
		$helper->fields_value['wtmainblogurl'] = Configuration::get('wtmainblogurl');
		$helper->fields_value['wtusehtml'] = Configuration::get('wtusehtml');
		$helper->fields_value['wtshowcolumn'] = Configuration::get('wtshowcolumn');
		$helper->fields_value['wtblogmetakeyword'] = Configuration::get('wtblogmetakeyword');
		$helper->fields_value['wtblogmetatitle'] = Configuration::get('wtblogmetatitle');
		$helper->fields_value['wtblogmetadescrip'] = Configuration::get('wtblogmetadescrip');
		$helper->fields_value['wtshowviewed'] = Configuration::get('wtshowviewed');
		$helper->fields_value['wtdisablecatimg'] = Configuration::get('wtdisablecatimg');
		$helper->fields_value['wtenablecomment'] = Configuration::get('wtenablecomment');
		$helper->fields_value['wtshownoimg'] = Configuration::get('wtshownoimg');
		$helper->fields_value['wtcaptchaoption'] = Configuration::get('wtcaptchaoption');
		return $helper;
	}
	protected function regenerateform()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->fields_form[0]['form'] = array(
			'legend' => array(
					'title' => $this->l('Blog Thumblr Configuration'),
			),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Delete Old Thumblr'),
					'name' => 'isdeleteoldthumblr',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'isdeleteoldthumblr',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'isdeleteoldthumblr',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				)
			),
			'submit' => array(
			'title' => $this->l('Re Generate Thumblr'),
			'class' => 'button btn btn-default pull-right'
			)
		);
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
					'id_lang' => $lang['id_lang'],
					'iso_code' => $lang['iso_code'],
					'name' => $lang['name'],
					'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->show_toolbar = false;
		$helper->submit_action = 'generateimage';
		$helper->fields_value['isdeleteoldthumblr'] = Configuration::get('isdeleteoldthumblr');
		return $helper;
	}
	public static function GetSmartBlogUrl()
	{
		$ssl_enable = Configuration::get('PS_SSL_ENABLED');
		$id_lang = (int)Context::getContext()->language->id;
		$id_shop = (int)Context::getContext()->shop->id;
		$rewrite_set = (int)Configuration::get('PS_REWRITING_SETTINGS');
		$ssl = null;
			static $force_ssl = null;
		if ($ssl === null)
		{
			if ($force_ssl === null)
				$force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
			$ssl = $force_ssl;
		}
		if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null)
			$shop = new Shop($id_shop);
		else
			$shop = Context::getContext()->shop;
		$base = (($ssl && $ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
		$lang_url = Language::getIsoById($id_lang).'/';
		if ((!$rewrite_set && in_array($id_shop, array((int)Context::getContext()->shop->id,  null))) || !Language::isMultiLanguageActivated($id_shop) || !(int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop))
			$lang_url = '';
		return $base.$shop->getBaseURI().$lang_url;
	}
	public static function GetSmartBlogLink($rewrite = 'wtblog', $params = null, $id_shop = null, $id_lang = null)
	{
		$url = wtblog::GetSmartBlogUrl();
		$dispatcher = Dispatcher::getInstance();
		if ($params != null)
			return $url.$dispatcher->createUrl($rewrite, $id_lang, $params);
		else
			return $url.$dispatcher->createUrl($rewrite);
	}
	public function addquickaccess()
	{
		$link = new Link();
		$qa = new QuickAccess();
		$qa->link = $link->getAdminLink('AdminModules').'&configure=wtblog';
		$languages = Language::getLanguages(false);
		foreach ($languages as $language)
			$qa->name[$language['id_lang']] = 'Wt Blog Setting';
		$qa->new_window = '0';
		if ($qa->save())
			Configuration::updateValue('wtblog_quick_access', $qa->id);
	}
	public function deletequickaccess()
	{
		$qa = new QuickAccess(Configuration::get('wtblog_quick_access'));
		$qa->delete();
	}
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wt_blog_category_shop (`id_wt_blog_category_shop`, `id_wt_blog_category`, `id_shop`)
		SELECT `id_wt_blog_category_shop`, `id_wt_blog_category`, '.(int)$params['new_id_shop'].'
		FROM '._DB_PREFIX_.'wt_blog_category_shop
		WHERE id_shop = '.(int)$params['old_id_shop']);
		
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wt_blog_comment_shop (`id_wt_blog_comment_shop`, `id_wt_blog_comment`, `id_shop`)
		SELECT `id_wt_blog_comment_shop`, `id_wt_blog_comment`, '.(int)$params['new_id_shop'].'
		FROM '._DB_PREFIX_.'wt_blog_comment_shop
		WHERE id_shop = '.(int)$params['old_id_shop']);
		
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wt_blog_post_shop (`id_wt_blog_post_shop`, `id_wt_blog_post`, `id_shop`)
		SELECT `id_wt_blog_post_shop`, `id_wt_blog_post`, '.(int)$params['new_id_shop'].'
		FROM '._DB_PREFIX_.'wt_blog_post_shop
		WHERE id_shop = '.(int)$params['old_id_shop']);
	}
	public function hookActionObjectLanguageAddAfter($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wt_blog_category_lang (`id_wt_blog_category`, `id_lang`, `meta_title`, `meta_keyword`, `meta_description`, `description`, `link_rewrite`)
		SELECT `id_wt_blog_category`, '.(int)$params['object']->id.', `meta_title`, `meta_keyword`, `meta_description`, `description`, `link_rewrite`
		FROM '._DB_PREFIX_.'wt_blog_category_lang
		WHERE id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT'));
	
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'wt_blog_post_lang (`id_wt_blog_post`, `id_lang`, `meta_title`, `meta_keyword`, `meta_description`, `short_description`, `content`, `link_rewrite`)
		SELECT `id_wt_blog_post`, '.(int)$params['object']->id.', `meta_title`, `meta_keyword`, `meta_description`, `short_description`, `content`, `link_rewrite`
		FROM '._DB_PREFIX_.'wt_blog_post_lang
		WHERE id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT'));
	}
}