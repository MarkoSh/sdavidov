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

function sya_init_yandex_share(current_language_iso_code, config) {
    function convert_config (config) {
        config = config || {};

        var popup_blocks = {};
        var popup_blocks_at_least_one = false;
        if (config.popup_blocks)
            for (var i = 0; i < config.popup_blocks.length; i++) {
                var popup_block = config.popup_blocks[i];
                if (popup_block.socials && popup_block.socials.length) {
                    popup_blocks[popup_block.title] = popup_blocks[popup_block.title] || [];
                    for (var j = 0; j < popup_block.socials.length; j++) {
                        popup_blocks[popup_block.title].push(popup_block.socials[j]);
                    }
                    popup_blocks_at_least_one = true;
                }
            }

        if (!popup_blocks_at_least_one && config.style !== 'none')
            popup_blocks = {
                '': ['vkontakte', 'facebook', 'twitter', 'odnoklassniki', 'moimir', 'lj']
            }

        var popupStyle = {
            blocks: popup_blocks,
            copyPasteField: config.copyPasteField || false,
        }

        if (config.vDirection && config.vDirection !== 'auto')
            popupStyle.vDirection = config.vDirection;


        return {
            l10n: current_language_iso_code || 'ru',
            theme: config.theme || 'default',
            elementStyle: {
                'type': config.style || 'none',
                'border': config.border || false,
                'linkUnderline': config.linkUnderline || false,
                'linkIcon': config.linkIcon || false,
                'quickServices': config.main_block || ['', 'vkontakte', 'twitter', 'facebook']
            },
            popupStyle: popupStyle
        };
    }

    var ya_config = convert_config(config);

    function sya_init_yandex_share_block() {
        var $element = $(this);
        var element_config = jQuery.extend({}, ya_config);
        element_config.element = this;
        element_config.link = $element.attr('yandex-share'),
        element_config.title = $element.data('title'),
        element_config.description = $element.data('description'),
        element_config.image = $element.data('image'),

        $.getScript('//yastatic.net/share/share.js', function () {
            new Ya.share(element_config);
        });
    }

    $(function () {
       $('[yandex-share]').each(sya_init_yandex_share_block);
    });
}