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
 *  @author    SeoSA <885588@bk.ru>
 *  @copyright 2012-2017 SeoSA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

(function () {

    angular.module('sya.core').filter('translate', TranslateFilter);

    var moduleName = 'sya';

    var init = function () {

        window[moduleName] =  window[moduleName] || {};
        window[moduleName].translations = [];
        window[moduleName].dumpTranslations = function () {
            var that =  window[moduleName];
            var smarty = 'var translations = {';
            smarty += "\n";
            for (var i =0; i < that.translations.length; i++) {
                var line =  that.translations[i];
                smarty += '"'+line+'": "{l s=\''+line+'\' mod=\'seosayandexservices\'}"';
                if (i < that.translations.length - 1) {
                    smarty += ',';
                }
                smarty += "\n";
            }
            smarty += '};';

            $('.translations-dump').remove();
            $('#content').append('<pre class="translations-dump">'+smarty+'</pre>');
        };

        init = angular.noop
    };

    TranslateFilter.$inject = ['translations', '_PS_MODE_DEV_'];
    function TranslateFilter(translations, _PS_MODE_DEV_) {

        if (_PS_MODE_DEV_)
            init();

        return function(str) {
            str = str || '';

            if (!_PS_MODE_DEV_)
                return translations[str] || str;

            if (!translations[str] && window[moduleName].translations.indexOf(str) == -1)
                window[moduleName].translations.push(str);

            return translations[str] || '__'+str.split(' ').join('_')+'__';
        }
    }
})();