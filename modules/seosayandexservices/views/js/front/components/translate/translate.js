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

function sya_init_yandex_translations (YANDEX_TRANSLATE_USER_LANGUAGE, YANDEX_TRANSLATE_SHOP_LANGUAGE, YANDEX_TRANSLATE_API, YANDEX_TRANSLATE_MODE, translations) {

    function l(str) {
        return translations[str] || str;
    }


    $(function () {
        if (YANDEX_TRANSLATE_USER_LANGUAGE === YANDEX_TRANSLATE_SHOP_LANGUAGE)
            return;

        function yandex_translation_request(text, html, cb)
        {
            $.ajax({
                url: YANDEX_TRANSLATE_API,
                dataType: 'json',
                data: {
                    text: text,
                    lang: YANDEX_TRANSLATE_SHOP_LANGUAGE+'-'+YANDEX_TRANSLATE_USER_LANGUAGE,
                    type: html ? 'html' : 'plain'
                },
                success: function(json){
                    cb(typeof html == 'string' ? json.text[0] : json.text);
                },
                error: function(){
                    cb()
                }
            });
        }

        function bind_event_listeners() {

            var hoverable = [
                '#short_description_content', // Short description
                '[itemtype="http://schema.org/Product"] .page-product-box .rte' // Full description on product page
            ].join(', ');

            function build_popup_html(revert)
            {
                var html = [
                    '<div class="yandex-translate-popup">',
                    '<span class="triangle"></span>' +
                    '<div class="trigger">' +
                    '<span class="logo"></span>' +
                    (
                        revert
                            ?
                       '<span class="text">'+l('Revert translation')+'</span>'
                            :
                        '<span class="text">'+l('Translate to')+' ' + YANDEX_TRANSLATE_USER_LANGUAGE.toString().toUpperCase()+'</<span>'
                    ),
                    '</div>',
                    '</div>'
                ];

                return html.join('');
            }

            function get_text_nodes_in(el) {
                return $(el).find(":not(iframe)").addBack().contents().filter(function() {
                    return this.nodeType == 3;
                });
            };

            function on_mouseenter (e) {

                var block = $(this);

                if (block.find('.yandex-translate-popup').length)
                    return;

                var popup = $(build_popup_html(block.is('.translated')));

                block.css({
                    position: 'relative'
                });

                var original_html = block.html();
                var original_text = block.text();


                var complete = function (content, type) {
                    block.data('untranslated', block.html());

                    block[type || 'html'](content);

                    popup.find('.text').text(l('Revert translation'));
                    block.addClass('translated');
                    block.append(popup);
                    popup.on('click', '.trigger', on_click);
                    popup.removeClass('loading');

                };

                var revert = function () {
                    popup.find('.text').text(l('Translate to')+' ' + YANDEX_TRANSLATE_USER_LANGUAGE.toString().toUpperCase());
                    block.html(block.data('untranslated'));
                    block.removeClass('translated');
                    block.append(popup);
                    popup.on('click', '.trigger', on_click);
                    popup.removeClass('loading');
                };

                function on_click (e) {
                    if (block.is('.translated')) {
                        popup.find('.text').text(l('Canceling'));
                        return revert();
                    }

                    var text, tmp, nodes, limit, done;

                    popup.addClass('loading');
                    popup.find('.text').text(l('Translating'));
                    switch (YANDEX_TRANSLATE_MODE) {
                        case 'plain':
                            yandex_translation_request(original_text, false, function (text) {
                                complete(text, 'text');
                            });
                            break;
                        case 'simple_html':
                            yandex_translation_request(original_html, false, complete);
                            break;
                        case 'smart_html':
                            tmp = $(original_html);
                            nodes = get_text_nodes_in(tmp);
                            limit = nodes.length-1;
                            done = 0;

                            nodes.each(function () {
                                var node = this;
                                yandex_translation_request(node.nodeValue, false, function (text) {
                                    node.nodeValue = text;
                                    done++;
                                    if (done >= limit) {
                                        complete(tmp);
                                    }
                                });
                            });
                            break;
                        case 'smart_html_fast':
                            text = [];
                            tmp = $(original_html);
                            nodes = get_text_nodes_in(tmp);
                            limit = nodes.length-1;
                            done = 0;

                            nodes.each(function () {
                                text.push(this.nodeValue)
                            });

                            yandex_translation_request(text, false, function (text) {
                                nodes.each(function (i) {
                                    this.nodeValue = text[i];
                                });

                                complete(tmp);
                            });
                            break;
                    }
                }

                popup.on('click', '.trigger', on_click);
                block.data('yandex-translate-popup', popup);
                block.append(popup)
            }

            function on_mouseleave(e)
            {
                var block = $(this);
                var popup = block.find('.yandex-translate-popup');
                if (block.find('.yandex-translate-popup').length)
                    popup.remove();

            }

            $('body')
                .on('mouseenter', hoverable, on_mouseenter)
                .on('mouseleave', hoverable, on_mouseleave)
        }


        bind_event_listeners();

    });

}

