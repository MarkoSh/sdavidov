{**
* 2015 Skrill
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
*  @author Skrill <contact@skrill.com>
*  @copyright  2015 Skrill
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<iframe style = "border:none; width:100%; height:1000px;" src="{$redirectUrl|escape:'htmlall':'UTF-8'}"></iframe>