<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 09/04/17
 * Time: 13:02
 */

class CategoryController extends CategoryControllerCore
{
    public function canonicalRedirection($canonicalURL = '')
    {
        if (Tools::getValue('live_edit'))
            return ;
        if (!Validate::isLoadedObject($this->category) || !$this->category->inShop() || !$this->category->isAssociatedToShop() || $this->category->id == Configuration::get('PS_ROOT_CATEGORY'))
        {
            $this->redirect_after = '404';
            $this->redirect();
        }
        if (!Tools::getValue('noredirect') && Validate::isLoadedObject($this->category))
            FrontController::canonicalRedirection($this->context->link->getCategoryLink($this->category));
    }
}