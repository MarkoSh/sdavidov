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

function sya_init_yandex_metrika (config, goals, translations, ps15) {
    translations = translations || {};
    window.sya_metrika =  window.sya_metrika || {};
    window.sya_metrika.translations = [];
    window.sya_metrika.dumpTranslations = function () {
        var that =  window.sya_metrika;
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
        $('body').append('<pre class="translations-dump">'+smarty+'</pre>');
    };


    window['yandex_metrika_callbacks'] = window['yandex_metrika_callbacks'] || [];

    window['yandex_metrika_callbacks'].push(on_ya_metrika_load);

    function l(str)
    {

        if (!translations[str] && window.sya_metrika.translations.indexOf(str) == -1)
            window.sya_metrika.translations.push(str);


        return translations[str] || str;
    }

    function on_ya_metrika_load()
    {
        var _metrika = Ya.Metrika;

        Ya.Metrika = function (options) {
            _metrika.apply(this, arguments);
            on_ya_counter_load(this);
        };
    }

    function on_ya_counter_load(counter)
    {
        function goal(type, params)
        {
            if (params)
            {
                var translated = {};
                for (var name in params)
                {
                    if (!params.hasOwnProperty(name)) continue;
                    translated[l(name)] = params[name];
                }

                params = translated;
            }

            console.log(type, params);
            counter.reachGoal(type, params);
        }

        if (goals && goals.length)
        {

            for (var i = 0; i < goals.length; i++) {
                goal(goals[i].type,goals[i].params)
            }
        }

        $(function () {
            (function (cart) {
                if (!cart) return;
                var _add = cart.add;
                cart.add = function (idProduct, idCombination) {
                    _add.apply(this, arguments);

                    var params = {
                        "Product ID": idProduct
                    };
                    if (idCombination)
                        params["Combination ID"] = idCombination;

                    goal('add_product_to_cart', params);
                };

                var _remove = cart.remove;
                cart.remove = function (idProduct, idCombination) {
                    _remove.apply(this, arguments);

                    var params = {
                        "Product ID": idProduct
                    };
                    if (idCombination)
                        params["Combination ID"] = idCombination;

                    goal('remove_product_from_cart', params);
                };
            })( window['ajaxCart']);

            (function (orderOpcUrl) {
                if (!orderOpcUrl) return;

                var listener;

                try {
                    if (ps15)
                        listener = jQuery._data( document.getElementById('submitAccount'), "events" ).click[0];
                    else
                        listener = jQuery._data( document, "events" ).click.filter(function (listener) {
                            return listener.selector === '#submitAccount, #submitGuestAccount'
                        })[0];
                } catch (e)
                {
                }
                if (!listener) return;

                window['sya_after_submit_opc_new_account'] = function (json) {
                    var isGuest = parseInt($('#is_new_customer').val()) == 1 ? 0 : 1;

                    if (json && !json.hasError && !isGuest)
                        goal('create_account');
                };

                var string = listener.handler.toString();
                var regex = /success:[\s\n]*?function[\s\n]*?\((.*?)\)[\s\n]*?\{/i;
                var replace = "success: function ($1)\n{ \nwindow['sya_after_submit_opc_new_account']($1) \n ";
                string = string.replace(regex, replace);

                try {
                    eval('window[\'sya_on_submit_opc_new_account\'] = '+string);
                    var new_handler = window['sya_on_submit_opc_new_account'];
                    window['sya_on_submit_opc_new_account'] = undefined;
                    if (ps15)
                        jQuery._data( document.getElementById('submitAccount'), "events" ).click[0].handler = new_handler;
                    else
                        jQuery._data( document, "events" ).click.filter(function (listener) {
                            return listener.selector === '#submitAccount, #submitGuestAccount'
                        })[0].handler = new_handler;
                } catch (e) {
                }

            })(window['orderOpcUrl']);
        });
    }
}