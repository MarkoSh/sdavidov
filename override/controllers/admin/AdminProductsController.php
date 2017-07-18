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
 * Class AdminProductsController
 */
class AdminProductsController extends AdminProductsControllerCore
{
	/**
	 * @return string|void
	 * @throws PrestaShopException
	 */
	/*
    * module: seosayandexservices
    * date: 2017-07-18 19:09:48
    * version: 1.2.1
    */
    public function renderForm()
	{
		
		$module = Module::getInstanceByName('seosayandexservices');
		if ($module->active && ($this->display == 'edit' || $this->display == 'add') && !$this->ajax)
		{
			unset($this->tpl_form_vars['product_tabs']['ModuleSeosayandexservices']);
			foreach ($module->getComponentsForHook('displayAdminProductsExtra') as $component)
			{
				$id = sprintf(
						'Module%s%s',
						Tools::ucfirst($module->name),
						Tools::ucfirst($component->getName())
				);
				$this->available_tabs[$id] = 24;
				$this->available_tabs_lang[$id] = $component->getName();
				$this->tpl_form_vars['product_tabs'][$id] = array(
						'id' => $id,
						'selected' => Tools::strtolower($id) == Tools::strtolower($this->tab_display),
						'name' => $component->getDisplayName(),
						'href' => sprintf(
								'%s&id_product=%d&action=Module%s&component=%s',
								$this->context->link->getAdminLink('AdminProducts'),
								(int)Tools::getValue('id_product'),
								Tools::ucfirst($module->name),
								$component->getName()
						)
				);
			}
		}
		return parent::renderForm();
	}
}