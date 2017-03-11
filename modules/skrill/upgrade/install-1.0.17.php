<?php
/**
* 2015 Skrill
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
*  @author    Skrill <contact@skrill.com>
*  @copyright 2015 Skrill
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Skrill
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_17($module)
{
    if (is_null($module->warning) && !$module->updateOrderRefTables()) {
        if ($module->l('ERROR_MESSAGE_CREATE_TABLE') == "ERROR_MESSAGE_CREATE_TABLE") {
            $module->warning = "There was an Error creating a custom table.";
        } else {
            $module->warning = $module->l('ERROR_MESSAGE_CREATE_TABLE');
        }
    }
    if (is_null($module->warning) && !$module->addSkrillOrderStatus()) {
        if ($module->l('ERROR_MESSAGE_CREATE_ORDER_STATUS') == "ERROR_MESSAGE_CREATE_ORDER_STATUS") {
            $module->warning = "There was an Error creating a custom order status.";
        } else {
            $module->warning = $module->l('ERROR_MESSAGE_CREATE_ORDER_STATUS');
        }
    }
    $defaultSort = 1;
    foreach (array_keys(SkrillPaymentCore::getPaymentMethods()) as $paymentType) {
        //default enable payment grop
        if ($paymentType == 'VSA' || $paymentType == 'MSC' ||
            $paymentType == 'AMX') {
            Configuration::updateValue('SKRILL_' . $paymentType . '_ACTIVE', '0');
        } else {
            Configuration::updateValue('SKRILL_' . $paymentType . '_ACTIVE', '1');
        }
        //default payment mode
        if ($paymentType != 'FLEXIBLE') {
            Configuration::updateValue('SKRILL_' . $paymentType . '_MODE', '1');
        }
        $defaultSort++;
    }
    return true;
}
