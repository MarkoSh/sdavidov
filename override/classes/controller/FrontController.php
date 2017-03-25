<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/03/17
 * Time: 22:17
 */

class FrontController extends FrontControllerCore
{
    public function setMedia()
    {
        parent::setMedia(); // CSS files
        FrontController::addJS('//cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js');
    }
}