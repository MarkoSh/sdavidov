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

class AdminBlogPostController extends AdminController
{
	public $asso_type = 'shop';

	public function __construct()
	{
		$this->table = 'wt_blog_post';
		$this->className = 'WtBlogPost';
		$this->lang = true;
		$this->image_dir = '../modules/wtblog/images';
		$this->context = Context::getContext();
		$this->_defaultOrderBy = 'created';
		$this->_defaultorderWay = 'DESC';
		$this->bootstrap = true;
			if (Shop::isFeatureActive())
				Shop::addTableAssociation($this->table, array('type' => 'shop'));
		parent::__construct();
		$this->fields_list = array(
			'id_wt_blog_post' => array(
					'title' => $this->l('Id'),
					'width' => 50,
					'type' => 'text',
					'orderby' => true,
					'filter' => true,
					'search' => true
			),
			'viewed' => array(
						'title' => $this->l('View'),
						'width' => 50,
						'type' => 'text',
						'lang' => true,
						'orderby' => true,
						'filter' => false,
						'search' => false
				),
				'image' => array(
						'title' => $this->l('Image'),
						'image' => $this->image_dir,
						'orderby' => false,
						'search' => false,
						'width' => 200,
						'align' => 'center',
						'orderby' => false,
						'filter' => false,
						'search' => false
				),
				'meta_title' => array(
						'title' => $this->l('Title'),
						'width' => 440,
						'type' => 'text',
						'lang' => true,
						'orderby' => true,
						'filter' => true,
						'search' => true
				),
				'created' => array(
						'title' => $this->l('Posted Date'),
						'width' => 100,
						'type' => 'date',
						'lang' => true,
						'orderby' => true,
						'filter' => true,
						'search' => true
				),
				'active' => array(
					'title' => $this->l('Status'),
					'width' => '70',
					'align' => 'center',
					'active' => 'status',
					'type' => 'bool',
					'orderby' => true,
					'filter' => true,
					'search' => true
				)
		);
		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'wt_blog_post_shop sbs ON a.id_wt_blog_post=sbs.id_wt_blog_post && sbs.id_shop IN('.implode(',', Shop::getContextListShopID()).')';
		$this->_select = 'sbs.id_shop';
		$this->_defaultOrderBy = 'a.id_wt_blog_post';
		$this->_defaultOrderWay = 'DESC';
		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
			$this->_group = 'GROUP BY a.wt_blog_post';
		parent::__construct();
	}
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		return parent::renderList();
	}
	public function postProcess()
	{
		$WtBlogPost = new WtBlogPost();
		$BlogPostCategory = new BlogPostCategory();
		
		if (Tools::isSubmit('deletewt_blog_post') && Tools::getValue('id_wt_blog_post') != '')
		{
			$WtBlogPost = new WtBlogPost((int)Tools::getValue('id_wt_blog_post'));
			
			if (!$WtBlogPost->delete())
				$this->errors[] = Tools::displayError('An error occurred while deleting the object.').$this->table.' ('.Db::getInstance()->getMsgError().')';
			else
			{
				Hook::exec('actionsbdeletepost', array('WtBlogPost' => $WtBlogPost));
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
			}
		}
		elseif (Tools::isSubmit('submitAddwt_blog_post'))
		{
			parent::validateRules();
			if (count($this->errors))
				return false;
			if (!$id_wt_blog_post = (int)Tools::getValue('id_wt_blog_post'))
			{
				$WtBlogPost = new $WtBlogPost();
				$id_lang_default = Configuration::get('PS_LANG_DEFAULT');
						$languages = Language::getLanguages(false);
				foreach ($languages as $language)
				{
					$title = str_replace('"', '', htmlspecialchars_decode(html_entity_decode(Tools::getValue('meta_title_'.$language['id_lang']))));
					$WtBlogPost->meta_title[$language['id_lang']] = $title;
					$WtBlogPost->meta_keyword[$language['id_lang']] = (string)Tools::getValue('meta_keyword_'.$language['id_lang']);
					$WtBlogPost->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
					$WtBlogPost->short_description[$language['id_lang']] = (string)Tools::getValue('short_description_'.$language['id_lang']);
					$WtBlogPost->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
					if (Tools::getValue('link_rewrite_'.$language['id_lang']) == '' && Tools::getValue('link_rewrite_'.$language['id_lang']) == null)
						$WtBlogPost->link_rewrite[$language['id_lang']] = str_replace(array(' ', ':', '\\', '/', '#', '!','*','.','?'), '-', Tools::getValue('meta_title_'.$id_lang_default));
					else
						$WtBlogPost->link_rewrite[$language['id_lang']] = str_replace(array(' ', ':', '\\', '/', '#', '!','*','.','?'), '-', Tools::getValue('link_rewrite_'.$language['id_lang']));
				}
				$WtBlogPost->id_parent = Tools::getValue('id_parent');   
				$WtBlogPost->position = 0;
				$WtBlogPost->active = Tools::getValue('active');
				
				$WtBlogPost->id_category = Tools::getValue('id_category');
				$WtBlogPost->comment_status = Tools::getValue('comment_status');
				$WtBlogPost->id_author = $this->context->employee->id;
				$WtBlogPost->created = Date('y-m-d H:i:s');
				$WtBlogPost->modified = Date('y-m-d H:i:s');
				$WtBlogPost->available = 1;
				$WtBlogPost->is_featured = Tools::getValue('is_featured');
				$WtBlogPost->viewed = 1;
				
				$WtBlogPost->post_type = Tools::getValue('post_type');
				
			if (!$WtBlogPost->save())
				$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
			else
			{
				Hook::exec('actionsbnewpost', array('WtBlogPost' => $WtBlogPost));
				$this->updateTags($languages, $WtBlogPost);
				$this->processImage($_FILES, $WtBlogPost->id);
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
			}
			}
			elseif ($id_wt_blog_post = Tools::getValue('id_wt_blog_post'))
			{
				$WtBlogPost = new $WtBlogPost($id_wt_blog_post);
				$languages = Language::getLanguages(false);
				foreach ($languages as $language)
				{
					$title = str_replace('"', '', htmlspecialchars_decode(html_entity_decode(Tools::getValue('meta_title_'.$language['id_lang']))));
					$WtBlogPost->meta_title[$language['id_lang']] = $title;
					$WtBlogPost->meta_keyword[$language['id_lang']] = Tools::getValue('meta_keyword_'.$language['id_lang']);
					$WtBlogPost->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
					$WtBlogPost->short_description[$language['id_lang']] = Tools::getValue('short_description_'.$language['id_lang']);
					$WtBlogPost->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
					$WtBlogPost->link_rewrite[$language['id_lang']] = str_replace(array(' ', ':', '\\', '/', '#', '!','*','.','?'), '-', Tools::getValue('link_rewrite_'.$language['id_lang']));
				}
				$WtBlogPost->is_featured = Tools::getValue('is_featured');
				$WtBlogPost->id_parent = Tools::getValue('id_parent');
				$WtBlogPost->active = Tools::getValue('active');
				$WtBlogPost->id_category = Tools::getValue('id_category');
				$WtBlogPost->comment_status = Tools::getValue('comment_status');
				$WtBlogPost->id_author = $this->context->employee->id;
				$WtBlogPost->modified = Date('y-m-d H:i:s');
				if (!$WtBlogPost->update())
					$this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
				else
					Hook::exec('actionsbupdatepost', array('WtBlogPost' => $WtBlogPost));
				$this->updateTags($languages, $WtBlogPost);
				$this->processImage($_FILES, $WtBlogPost->id_wt_blog_post);
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
			}
		}
		elseif (Tools::isSubmit('statuswt_blog_post') && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->toggleStatus())
					{
						Hook::exec('actionsbtogglepost', array('WtBlogPost' => $this->object));
						$identifier = ((int)$object->id_parent ? '&id_wt_blog_post='.(int)$object->id_parent : '');
						Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
					}
					else
						$this->errors[] = Tools::displayError('An error occurred while updating the status.');
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		elseif (Tools::isSubmit('wt_blog_postOrderby') && Tools::isSubmit('wt_blog_postOrderway'))
		{
			$this->_defaultOrderBy = Tools::getValue('wt_blog_postOrderby');
			$this->_defaultOrderWay = Tools::getValue('wt_blog_postOrderway');
		}
	}

	public function processImage($FILES, $id)
	{
		if (isset($FILES['image']) && isset($FILES['image']['tmp_name']) && !empty($FILES['image']['tmp_name']))
		{
			if ($error = ImageManager::validateUpload($FILES['image'], 4000000))
				return $this->displayError($this->l('Invalid image'));
			else
			{
				$ext = Tools::substr($FILES['image']['name'], strrpos($FILES['image']['name'], '.') + 1);
				$file_name = $id.'.'.$ext;
				$path = _PS_MODULE_DIR_.'wtblog/views/img/'.$file_name;
				if (!move_uploaded_file($FILES['image']['tmp_name'], $path))
					return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
				else
				{
					$posts_types = BlogImageType::GetImageAllType('post');
					foreach ($posts_types as $image_type)
					{
						$dir = _PS_MODULE_DIR_.'wtblog/views/img/'.$id.'-'.Tools::stripslashes($image_type['type_name']).'.jpg';
						if (file_exists($dir))
							unlink($dir);
					}
					foreach ($posts_types as $image_type)
						ImageManager::resize($path, _PS_MODULE_DIR_.'wtblog/views/img/'.$id.'-'.Tools::stripslashes($image_type['type_name']).'.jpg', (int)$image_type['width'], (int)$image_type['height']);
				}
			}
		}
	}
	
	public function renderForm() 
	{
		$img_desc = '';
		$img_desc .= $this->l('Upload a Feature Image from your computer. Only jpg image is allowed');
		
		if (Tools::getvalue('id_wt_blog_post') != '' && Tools::getvalue('id_wt_blog_post') != null)
		{
			$img_post = Tools::getvalue('id_wt_blog_post');
			if (preg_match('/sample/', $img_post.'jpg') === 0)
				if ($img_post && file_exists(_PS_MODULE_DIR_.'wtblog/views/img/'.$img_post.'.jpg'))
					$img_desc .= '<br/><img style="height:250px;width:auto;clear:both;border:1px solid black;" alt="" src="'.__PS_BASE_URI__.'modules/wtblog/views/img/'.$img_post.'.jpg" /><br />';
		}
		$this->fields_form = array(
			'legend' => array(
			'title' => $this->l('Blog Post'),
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'post_type',
					'default_value' => 0,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Blog Title'),
					'name' => 'meta_title',
					'size' => 60,
					'required' => true,
					'desc' => $this->l('Enter Your Blog Post Title'),
					'lang' => true,
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description'),
					'name' => 'content',
					'lang' => true,
					'rows' => 10,
					'cols' => 62,
					'class' => 'rte',
					'autoload_rte' => true,
					'required' => true,
					'desc' => $this->l('Enter Your Post Description')
				),
				array(
					'type' => 'file',
					'label' => $this->l('Feature Image:'),
					'name' => 'image',
					'display_image' => false,
					'desc' => $img_desc
				),
				array(
					'type' => 'select',
					'label' => $this->l('Blog Category'),
					'name' => 'id_category',
					'options' => array(
						'query' => BlogCategory::getCategory(),
						'id' => 'id_wt_blog_category',
						'name' => 'meta_title'
					),
					'desc' => $this->l('Select Your Parent Category')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta Keyword'),
					'name' => 'meta_keyword',
					'lang' => true,
					'size' => 60,
					'required' => false,
					'desc' => $this->l('Enter Your Post Meta Keyword. Separated by comma(,)')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Short Description'),
					'name' => 'short_description',
					'rows' => 10,
					'cols' => 62,
					'lang' => true,
					'required' => true,
					'desc' => $this->l('Enter Your Post Short Description')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Meta Description'),
					'name' => 'meta_description',
					'rows' => 10,
					'cols' => 62,
					'lang' => true,
					'required' => false,
					'desc' => $this->l('Enter Your Post Meta Description')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Link Rewrite'),
					'name' => 'link_rewrite',
					'size' => 60,
					'lang' => true,
					'required' => false,
					'desc' => $this->l('Enetr Your Post Slug. Use In SEO Friendly URL')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Tag'),
					'name' => 'tags',
					'size' => 60,
					'lang' => true,
					'required' => false,
					'desc' => $this->l('Enter Your Post Meta Tag. Separated by comma(,)')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Comment Status'),
					'name' => 'comment_status',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'active',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'active',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
						),
					'desc' => $this->l('You Can Enable or Disable Your Comments')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'active',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'active',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
						)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Is Featured?'),
					'name' => 'is_featured',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
						'id' => 'is_featured',
						'value' => 1,
						'label' => $this->l('Enabled')
						),
						array(
						'id' => 'is_featured',
						'value' => 0,
						'label' => $this->l('Disabled')
						)
					)
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);
		
		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
			);
		}
		if (!($WtBlogPost = $this->loadObject(true)))
			return;
		
		$this->fields_form['submit'] = array(
			'title' => $this->l('Save'),
			'class' => 'btn btn-default pull-right'
		);
		$image = ImageManager::thumbnail(_MODULE_SMARTBLOG_DIR_.$WtBlogPost->id_wt_blog_post.'.jpg', $this->table.'_'.(int)$WtBlogPost->id_wt_blog_post.'.'.$this->imageType, 350, $this->imageType, true);
		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_MODULE_SMARTBLOG_DIR_.$WtBlogPost->id_wt_blog_post.'.jpg') / 1000 : false
		);
			if (Tools::getvalue('id_wt_blog_post') != '' && Tools::getvalue('id_wt_blog_post') != null)
			{
				foreach (Language::getLanguages(false) as $lang)
					$this->fields_value['tags'][(int)$lang['id_lang']] = WtBlogPost::getProductTagsBylang((int)Tools::getvalue('id_wt_blog_post'), (int)$lang['id_lang']);
			}
		return parent::renderForm();
	}

	public function initToolbar()
	{
		parent::initToolbar();
	}
	public function updateTags($languages, $post)
	{
		$tag_success = true;
		if (!WtBlogPost::deleteTagsForProduct((int)$post->id))
			$this->errors[] = Tools::displayError('An error occurred while attempting to delete previous tags.');
		foreach ($languages as $language)
			if ($value = Tools::getValue('tags_'.$language['id_lang']))
				$tag_success &= WtBlogPost::addTags($language['id_lang'], (int)$post->id, $value);
		if (!$tag_success)
			$this->errors[] = Tools::displayError('An error occurred while adding tags.');
		return $tag_success;
	}
}