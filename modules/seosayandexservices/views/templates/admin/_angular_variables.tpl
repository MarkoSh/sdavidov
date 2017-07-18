{*
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
*}
<script type="application/javascript">
    (function () {
        angular.module('sya.core').constant('current_language_iso_code', {$current_language_iso_code|json_encode});
        angular.module('sya.core').constant('site_logo_image_url', {$site_logo_image_url|json_encode});
        angular.module('sya.core').constant('api_url', {$yandex_admin_controller_url|json_encode});
        angular.module('sya.core').constant('base_url', {$shop_base_url|json_encode});
        angular.module('sya.core').constant('_PS_MODE_DEV_', {constant('_PS_MODE_DEV_')|json_encode});
        {strip}angular.module('sya.core').constant('yandex_components', {
            {foreach name="comonents" from=$components item="component"}
            {$component->getName()|json_encode}: {$component->toArray()|json_encode}{if not $smarty.foreach.comonents.last},{/if}
            {/foreach}
        });{/strip}
{foreach from=$angular_values key='key' item="value"}
        angular.module('sya.core').constant('{$key|escape:'htmlall':'UTF-8'}', {$value|json_encode});
{/foreach}
    })();
</script>
