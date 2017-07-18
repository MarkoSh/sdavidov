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

function sya_init_yandex_bo_translations (YANDEX_TRANSLATE_API, translations) {

    function l(str) {
        return translations[str] || str;
    }

    function sya_yandex_transaltions()
    {
        function group_translateble_fields()
        {
            var field = $(this);

            var group = field[field.is('.form-group') ? 'parents' : 'closest']('.form-group');

            if (!group.is('.translateble-group'))
                group.addClass('translateble-group');
        }

        function get_field_id_lang(field)
        {
            return field.attr('class').match(/lang-([0-9]+)/i)[1] | 0;
        }

        function get_field_lang_iso(field)
        {
            return field.find('button.dropdown-toggle:not(.yandex-dropdown-toggle)').text().trim();
        }

        function find_languages(group)
        {
            var languages = [];

            group.find('.translatable-field').each(function () {
                var field = $(this);

                var lang = {};

                lang.iso = get_field_lang_iso(field);
                lang.id = get_field_id_lang(field);

                var name_regex = new RegExp('hideOtherLanguage\\('+lang.id+'\\)');
                field.find('ul.dropdown-menu li a').each(function () {
                    var $a = $(this);
                    var href = $a.attr('href');

                    if (name_regex.test(href))
                    {
                        lang.name = $a.text().trim();
                        return false;
                    }
                })

                languages.push(lang)
            })

            return languages;
        }

        function build_dropdown_html(languages)
        {
            var html = [
                '<div class="btn-group yandex-translate-menu">',
                '<button type="button" tabindex="-1" class="icon-yandex-translate yandex-dropdown-toggle dropdown-toggle" data-toggle="dropdown">',
                '</button>',
                '<ul class="dropdown-menu">',
            ]

            for (var i = 0; i < languages.length; i++)
                html.push('<li><a data-id="'+languages[i].id+'" data-iso="'+languages[i].iso+'">'+l('Fill current from')+' '+languages[i].name+'</a></li>');

            html.push('<li><a class="fill-from-current">'+l('Fill other languages from current')+'</a></li>');

            html.push('</ul>');
            html.push('</div>');

            return html.join("");
        }



        function init_yandex_translations()
        {
            var group  = $(this);

            if (group.is('.translateble-group-ready') || group.is('.translateble-group-error'))
                return;

            function error()
            {
                group.addClass('translateble-group-error');
            }

            function done()
            {
                group.addClass('translateble-group-ready');
            }

            var languages = find_languages(group)
            if (!languages.length)
                return error();

            group.data('languages', languages);
            var dropdown_html = build_dropdown_html(languages);

            group.find('.translatable-field').each(function () {
                var field = $(this);

                field.find('.col-lg-2')
                    .removeClass('col-lg-2')
                    .addClass('btn-group')
                    .wrap('<div class="col-lg-3"></div>').wrap('<div class="btn-group"></div>')
                    .after(dropdown_html)

            });

            return done();
        }

        function get_input_interface_from_group(group, id_lang)
        {
            var field = group.find('.translatable-field.lang-' + id_lang);
            var input = field.find('input[type="text"], textarea');

            if (!input.length)
                return false;

            var iface = {
                set: function (value) {
                    input.val(value);
                    return iface;
                },
                get: function () {
                    return input.val();
                }
            }

            if (field.find('.mce-tinymce').length)
            {
                var tiny_mce = tinyMCE.get(input.attr('id'));

                iface.get = function () {
                    return tiny_mce.getContent();
                };

                iface.set = function (value) {
                    tiny_mce.setContent(value, {format : 'raw'});
                    return iface;
                }

                iface.html = true;
            }
            else if (field.find('.tagify-container').length)
            {
                var tagify = input.data('uiTagify');

                iface.get = function () {
                    return tagify.serialize()
                };

                iface.set = function (value) {
                    $.each(tagify.tags, function(i, tag) {
                        tagify.remove(i);
                    });

                    $.each(value.split(tagify.options.outputDelimiter), function(i, tag) {
                        tagify.add(tag);
                    });

                    return iface;
                }
            }
            else
            {
                iface.get = function () {
                    return input.val();
                };

                iface.set = function (value) {
                    input.val(value);
                    return iface;
                };

                iface.html = false
            }


            return iface;
        }

        function yandex_translation_request(text, from, to, html, cb)
        {
            $.ajax({
                url: YANDEX_TRANSLATE_API,
                dataType: 'json',
                data: {
                    text: text,
                    lang: from+'-'+to,
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

        function init_yandex_events() {
            $document = $(document);

            function get_active_lang(group)
            {
                var active_field = group.find('.translatable-field:visible');

                return {
                    id: get_field_id_lang(active_field),
                    iso: get_field_lang_iso(active_field),
                }
            }

            function transalte_from_other_lang($a, cb)
            {
                var from_iso = $a.data('iso');
                var from_id = $a.data('id');
                var group = $a.closest('.translateble-group');
                var menu = group.find('.yandex-translate-menu');
                var btn = menu.find('button');

                var to_lang = get_active_lang(group);

                if (to_lang.id !== from_id)
                {
                    var from_input = get_input_interface_from_group(group, from_id);
                    var to_input = get_input_interface_from_group(group, to_lang.id);
                    if (from_input && to_input)
                    {
                        var from_value = from_input.get();
                        if (from_value)
                        {
                            btn.addClass('loading');
                            yandex_translation_request(
                                from_value,
                                from_iso,
                                to_lang.iso,
                                from_input.html,
                                function (value) {
                                    if (value)
                                        to_input.set(value)

                                    btn.removeClass('loading');
                                    cb();
                                }
                            )
                            return;
                        }
                    }
                }

                cb();
            }

            function transalte_from_current_lang($a, cb) {
                var group = $a.closest('.translateble-group');
                var menu = group.find('.yandex-translate-menu');
                var btn = menu.find('button');

                var current_lang = get_active_lang(group);
                var current_input = get_input_interface_from_group(group, current_lang.id);
                var current_value = current_input.get();

                var target_languages = [];
                var group_languages = group.data('languages');
                for (var i = 0; i < group_languages.length; i++) {
                    if (group_languages[i].id !== current_lang.id) {
                        target_languages.push($.extend({}, group_languages[i]))
                    }
                }

                (function do_request()
                {
                    var target_language = target_languages.shift();
                    if (target_language) {
                        var input = get_input_interface_from_group(group, target_language.id);

                        btn.addClass('loading');
                        yandex_translation_request(
                            current_value,
                            current_lang.iso,
                            target_language.iso,
                            current_input.html,
                            function (value) {
                                if (value)
                                    input.set(value)

                                current_input.set(value);

                                setTimeout(function () {
                                    do_request();
                                }, 300)
                            }
                        )
                    } else {
                        current_input.set(current_value);
                        btn.removeClass('loading');
                        cb();
                    }
                })();
            }

            $document.on('click', '.yandex-translate-menu .dropdown-menu a', function (e) {
                e.preventDefault();
                var $a = $(this);
                var group = $a.closest('.translateble-group');
                var menu = group.find('.yandex-translate-menu');
                var btn = menu.find('button');

                if (btn.is(':disabled'))
                    return;

                btn.attr('disabled', 'disabled');
                var cb = function () {
                    btn.removeAttr('disabled');
                }

                if ($a.is('.fill-from-current'))
                    transalte_from_current_lang($a, cb)
                else
                    transalte_from_other_lang($a, cb)
            })
        }

        $('.translatable-field').each(group_translateble_fields);
        $('.translateble-group').each(init_yandex_translations);

        init_yandex_events();
    }


    $(sya_yandex_transaltions)
}