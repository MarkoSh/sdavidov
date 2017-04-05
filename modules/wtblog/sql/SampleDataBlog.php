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

class SampleDataBlog
{
	public function SampleDataInstall()
	{
		$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
		$damisql = 'INSERT INTO '._DB_PREFIX_.'wt_blog_category (id_parent,active) VALUES (0,1);';
		Db::getInstance()->execute($damisql); 
		$damisq1l = 'INSERT INTO '._DB_PREFIX_.'wt_blog_category_shop (id_wt_blog_category,id_shop) VALUES (1,"'.$id_shop.'");';
		Db::getInstance()->execute($damisq1l); 
		$languages = Language::getLanguages(false);
		foreach ($languages as $language)
		{
			$damisql2 = 'INSERT INTO '._DB_PREFIX_.'wt_blog_category_lang (id_wt_blog_category,meta_title,id_lang,link_rewrite) VALUES (1,"Uncategories",'.(int)$language['id_lang'].',"uncategories");';
			Db::getInstance()->execute($damisql2);
		}
		for ($i = 1; $i <= 4; $i++)
		{
			Db::getInstance()->Execute('
					INSERT INTO `'._DB_PREFIX_.'wt_blog_post`(`id_author`, `id_category`, `position`, `active`, `available`, `created`, `viewed`, `comment_status`, `post_type`) 
					VALUES(1,1,0,1,1,"'.Date('y-m-d H:i:s').'",0,1,0)');
		}
								
		$languages = Language::getLanguages(false);
			for ($i = 1; $i <= 4; $i++)
			{
				if ($i == 1)
				{
					$title = 'From Now we are certified web agency';
					$slug = 'from_now_we_are_certified_web_agency';
					$des = 'Smartdatasoft is an offshore web development company located in Bangladesh. We are serving this sector since 2010. Our team is committed to develop high quality web based application and theme for our clients and also for the global marketplace. As your web development partner we will assist you in planning, development, implementation and upgrade! Why Smartdatasoft? Smartdatasoft released their first prestashop theme in November 2012. Till now we have 6+ prestashop theme which are getting sold on global renowned marketplace. Those themes are getting used in more than 400 customers eCommerce websites. Those themes are very user friendly and highly customize able from admin dashboard. For these reason these theme are very popular among the end users and developers';
				}
				elseif ($i == 2)
				{
					$title = 'What is Bootstrap? – The History and the Hype';
					$slug = 'what_is_bootstrap';
					$des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
				}
				elseif ($i == 3)
				{
					$title = 'Answers to your Questions about PrestaShop 1.6';
					$slug = 'answer_to_your_question_about_prestashop_1_6';
					$des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
				}
				elseif ($i == 4)
				{
					$title = 'Share the Love for PrestaShop 1.6';
					$slug = 'share_the_love_for_prestashop_1_6';
					$des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.'; 
				}
				elseif ($i == 5)
				{
					$title = 'Christmas Sale is here 5';
					$slug = 'Another-title-here-5';
					$des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
				}
				elseif ($i == 6)
				{
					$title = 'Christmas Sale is here 6';
					$slug = 'Another-title-here-6';
					$des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
				}
				elseif ($i == 7)
				{
					$title = 'Christmas Sale is here 7';
					$slug = 'Another-title-here-7';
					$des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
				}
				foreach ($languages as $language)
				{
					if (!Db::getInstance()->Execute('
					INSERT INTO `'._DB_PREFIX_.'wt_blog_post_lang`(`id_wt_blog_post`,`id_lang`,`meta_title`,`meta_description`,`short_description`,`content`,`link_rewrite`)
					VALUES('.$i.','.(int)$language['id_lang'].', 
					"'.htmlspecialchars($title).'", 
					"'.htmlspecialchars($des).'","'.Tools::substr($des, 0, 200).'","'.htmlspecialchars($des).'","'.$slug.'"
					)'))
						return false;
				}
			}
			for ($i = 1; $i <= 4; $i++)
			{
				Db::getInstance()->Execute('
					INSERT INTO `'._DB_PREFIX_.'wt_blog_post_shop`(`id_wt_blog_post`, `id_shop`) 
					VALUES('.$i.','.$id_shop.')');
			}
			for ($i = 1; $i <= 7; $i++)
			{
				if ($i == 1)
				{
					$type_name = 'home-default';
					$width = '240';
					$height = '160';
					$type = 'post';
				}
				elseif ($i == 2)
				{
					$type_name = 'home-small';
					$width = '65';
					$height = '45';
					$type = 'post';
				}
				elseif ($i == 3)
				{
					$type_name = 'single-default';
					$width = '840';
					$height = '420';
					$type = 'post';
				}
				elseif ($i == 4)
				{
					$type_name = 'home-small';
					$width = '65';
					$height = '45';
					$type = 'Category';
				}
				elseif ($i == 5)
				{
					$type_name = 'home-default';
					$width = '240';
					$height = '160';
					$type = 'Category';
				}
				elseif ($i == 6)
				{
					$type_name = 'single-default';
					$width = '840';
					$height = '420';
					$type = 'Category';
				}
				elseif ($i == 7)
				{
					$type_name = 'author-default';
					$width = '54';
					$height = '54';
					$type = 'Author';
				}	
				$damiimgtype = 'INSERT INTO '._DB_PREFIX_.'wt_blog_imagetype (type_name,width,height,type,active) VALUES ("'.$type_name.'","'.$width.'","'.$height.'","'.$type.'",1);';
				Db::getInstance()->execute($damiimgtype); 
			}
			return true;
	}
}
?>