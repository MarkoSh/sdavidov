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

function sya_init_yandex_map(current_language_iso_code, config) {
    function init_map($element, options) {
        var controls = [];
        for (var name in options.controls) {
            if (!options.controls.hasOwnProperty(name)) continue;

            if (options.controls[name])
                controls.push(name.replace(/(\_\w)/g, function(match){return match[1].toUpperCase();}))
        }

        return new ymaps.Map($element.get(0), {
            center: [options.center.lat, options.center.lng],
            zoom: (options.zoom | 0) || 7,
            type: options.type,
            controls: controls
        });
    }

    function init_placemark(options, draggable) {
        var geo_object_config = {
            geometry: {
                type: "Point",
                coordinates: [options.position.lat, options.position.lng]
            },
            properties: {
                iconContent: options.content
            }
        };

        var geo_object_params = {
            preset: options.style,
            draggable: !!draggable
        };

        return new ymaps.GeoObject(geo_object_config, geo_object_params);
    }

    function init() {
        var $element = $('<div style="width: 100%; height: 400px; margin-bottom: 15px" class="yandex-map"></div>');
        $element.insertBefore($(".contact-form-box:first"));

        var map = init_map($element, config);
        var placemark = init_placemark(config.placemark, false);
        map.geoObjects.add(placemark);

        $element.show();
    }

    $.getScript('https://api-maps.yandex.ru/2.1/?lang=' + current_language_iso_code, function () {
        ymaps.ready(init);
    });
}