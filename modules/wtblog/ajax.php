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

require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once (_PS_MODULE_DIR_.'wtblog/wtblog.php');
switch (Tools::getValue('action'))
	{
	case 'postcomment' :
		_posts();
		break;
	default:
		exit;
	}
exit;

	function _posts()
	{
		$WtBlogPost = new WtBlogPost();
		$WtBlog = new WtBlog();
		$array_error = array();
		$id_lang = (int)Context::getContext()->language->id;
		$id_post = Tools::getValue('id_post');
		$post = $WtBlogPost->getPost($id_post, $id_lang);
		if ($post['comment_status'] == 1)
		{
			$blogcomment = new Blogcomment();
			$name = Tools::getValue('name');
			$comment = Tools::getValue('comment');
			$mail = Tools::getValue('mail');
			$captcha = Tools::getvalue('wtblogcaptcha');
			$m_captcha = '123456';
			if (Tools::getValue('website') == '')
				$website = '#';
			else
				$website = Tools::getValue('website');
			$id_parent_post = (int)Tools::getValue('id_parent_post');
			if (empty($name))
				$array_error['name'] = $WtBlog->l('Name is required');
			
			if (empty($comment))
				$array_error['comment'] = $WtBlog->l('Comment must be between 25 and 1500 characters!');
			if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
				$array_error['mail'] = $WtBlog->l('E-mail address not valid !');
			if (Configuration::get('wtcaptchaoption') == '1')
				if ($captcha != $m_captcha)
					$array_error['captcha'] = $WtBlog->l('Captcha is not valid');
			
			if (is_array($array_error) && count($array_error))
			{
				$array_error['common'] = $WtBlog->l('Warning: Please check required form bellow!');
				die(Tools::jsonEncode( array('error'=> $array_error)));
			}
			else
			{
				$array_success = array();
				$comments = array();
				$comments['name'] = $name;
				$comments['mail'] = $mail;
				$comments['comment'] = $comment;
				$comments['website'] = $website;
				if (!$id_parent_post = Tools::getvalue('comment_parent'))
					$id_parent_post = 0;
				$value = Configuration::get('wtacceptcomment');
				if (Configuration::get('wtacceptcomment') != '' && Configuration::get('wtacceptcomment') != null)
					$value = Configuration::get('wtacceptcomment');
				else
					$value = 0;
					$bc = new Blogcomment();
					$bc->id_post = (int)$id_post;
					$bc->name = $name;
					$bc->email = $mail;
					$bc->content = $comment;
					$bc->website = $website;
					$bc->id_parent = (int)$id_parent_post;
					$bc->active = (int)$value;
					$bc->created = Date('y-m-d H:i:s');
					if ($bc->add())
					{
						$array_success['common'] = $WtBlog->l('Your comment successfully submitted.');
						$array_success['success'] = $WtBlog->l('Your comment successfully submitted'); 
						Hook::exec('actionsbpostcomment', array('bc' => $bc));
						die( Tools::jsonEncode( $array_success));
					}
			}
		}

	}
	function wtsendMail($sname, $semailAddr, $scomment, $slink = null)
	{
		$name = Tools::stripslashes($sname);
		$e_body = 'You have Received a New Comment In Your Blog Post From '.$name.'. Comment: '.$scomment.' .Your Can reply Here : '.$slink.'';
		$emailAddr = Tools::stripslashes($semailAddr);
		$comment = Tools::stripslashes($scomment);
		$subject = 'New Comment Posted';
		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$to = Configuration::get('PS_SHOP_EMAIL');	
		if (Mail::Send($id_lang, 'contact', $subject, array('{message}' => nl2br($e_body), '{email}' =>  $emailAddr), $to, null, $emailAddr, $name))
			return true;
	}
?>