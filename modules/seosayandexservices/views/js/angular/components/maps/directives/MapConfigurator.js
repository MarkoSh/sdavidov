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
    angular.module('sya.maps')
        .directive('mapsMapConfigurator', MapConfiguratorDirective);


    MapConfiguratorDirective.$inject = ['$q', 'current_language_iso_code', 'Tools', 'PromiseQueue', '$timeout'];
    function MapConfiguratorDirective($q, current_language_iso_code, Tools, PromiseQueue, $timeout) {

        function load_api() {
            if (!load_api.promise) {
                var deferred = $q.defer();

                $.getScript('https://api-maps.yandex.ru/2.1/?lang=' + current_language_iso_code, deferred.resolve);

                load_api.promise = deferred.promise
            }

            return load_api.promise
        }

        function init_map($element, options) {
            var controls = [];
            for (var name in options.controls) {
                if (!options.controls.hasOwnProperty(name)) continue;

                if (options.controls[name])
                    controls.push(Tools.underscoreToCamelCase(name))
            }

            return new ymaps.Map($element.find('yandex-map').get(0), {
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


        function link($scope, $element, $attrs) {

            var timeout_queue = new PromiseQueue($timeout);


            $element.hide();

            $scope.mapStyles = {
                'yandex#map': "Schema",
                'yandex#satellite': "Satellite",
                'yandex#hybrid': "Hybrid",
                'yandex#publicMap': "Public map",
                'yandex#publicMapHybrid': "Public hybrid"
            };

            $scope.placeMarkStyles = {
                'islands#blueIcon': 'islands#blueIcon',
                'islands#darkGreenIcon': 'islands#darkGreenIcon',
                'islands#redIcon': 'islands#redIcon',
                'islands#violetIcon': 'islands#violetIcon',
                'islands#darkOrangeIcon': 'islands#darkOrangeIcon',
                'islands#blackIcon': 'islands#blackIcon',
                'islands#nightIcon': 'islands#nightIcon',
                'islands#yellowIcon': 'islands#yellowIcon',
                'islands#darkBlueIcon': 'islands#darkBlueIcon',
                'islands#greenIcon': 'islands#greenIcon',
                'islands#pinkIcon': 'islands#pinkIcon',
                'islands#orangeIcon': 'islands#orangeIcon',
                'islands#grayIcon': 'islands#grayIcon',
                'islands#lightBlueIcon': 'islands#lightBlueIcon',
                'islands#brownIcon': 'islands#brownIcon',
                'islands#oliveIcon': 'islands#oliveIcon',
                'islands#blueDotIcon': 'islands#blueDotIcon',
                'islands#darkGreenDotIcon': 'islands#darkGreenDotIcon',
                'islands#redDotIcon': 'islands#redDotIcon',
                'islands#violetDotIcon': 'islands#violetDotIcon',
                'islands#darkOrangeDotIcon': 'islands#darkOrangeDotIcon',
                'islands#blackDotIcon': 'islands#blackDotIcon',
                'islands#nightDotIcon': 'islands#nightDotIcon',
                'islands#yellowDotIcon': 'islands#yellowDotIcon',
                'islands#darkBlueDotIcon': 'islands#darkBlueDotIcon',
                'islands#greenDotIcon': 'islands#greenDotIcon',
                'islands#pinkDotIcon': 'islands#pinkDotIcon',
                'islands#orangeDotIcon': 'islands#orangeDotIcon',
                'islands#grayDotIcon': 'islands#grayDotIcon',
                'islands#lightBlueDotIcon': 'islands#lightBlueDotIcon',
                'islands#brownDotIcon': 'islands#brownDotIcon',
                'islands#oliveDotIcon': 'islands#oliveDotIcon',
                'islands#blueCircleIcon': 'islands#blueCircleIcon',
                'islands#darkGreenCircleIcon': 'islands#darkGreenCircleIcon',
                'islands#redCircleIcon': 'islands#redCircleIcon',
                'islands#violetCircleIcon': 'islands#violetCircleIcon',
                'islands#darkOrangeCircleIcon': 'islands#darkOrangeCircleIcon',
                'islands#blackCircleIcon': 'islands#blackCircleIcon',
                'islands#nightCircleIcon': 'islands#nightCircleIcon',
                'islands#yellowCircleIcon': 'islands#yellowCircleIcon',
                'islands#darkBlueCircleIcon': 'islands#darkBlueCircleIcon',
                'islands#greenCircleIcon': 'islands#greenCircleIcon',
                'islands#pinkCircleIcon': 'islands#pinkCircleIcon',
                'islands#orangeCircleIcon': 'islands#orangeCircleIcon',
                'islands#grayCircleIcon': 'islands#grayCircleIcon',
                'islands#lightBlueCircleIcon': 'islands#lightBlueCircleIcon',
                'islands#brownCircleIcon': 'islands#brownCircleIcon',
                'islands#oliveCircleIcon': 'islands#oliveCircleIcon',
                'islands#blueCircleDotIcon': 'islands#blueCircleDotIcon',
                'islands#darkGreenCircleDotIcon': 'islands#darkGreenCircleDotIcon',
                'islands#redCircleDotIcon': 'islands#redCircleDotIcon',
                'islands#violetCircleDotIcon': 'islands#violetCircleDotIcon',
                'islands#darkOrangeCircleDotIcon': 'islands#darkOrangeCircleDotIcon',
                'islands#blackCircleDotIcon': 'islands#blackCircleDotIcon',
                'islands#nightCircleDotIcon': 'islands#nightCircleDotIcon',
                'islands#yellowCircleDotIcon': 'islands#yellowCircleDotIcon',
                'islands#darkBlueCircleDotIcon': 'islands#darkBlueCircleDotIcon',
                'islands#greenCircleDotIcon': 'islands#greenCircleDotIcon',
                'islands#pinkCircleDotIcon': 'islands#pinkCircleDotIcon',
                'islands#orangeCircleDotIcon': 'islands#orangeCircleDotIcon',
                'islands#grayCircleDotIcon': 'islands#grayCircleDotIcon',
                'islands#lightBlueCircleDotIcon': 'islands#lightBlueCircleDotIcon',
                'islands#brownCircleDotIcon': 'islands#brownCircleDotIcon',
                'islands#oliveCircleDotIcon': 'islands#oliveCircleDotIcon',
                'islands#blueStretchyIcon': 'islands#blueStretchyIcon',
                'islands#darkGreenStretchyIcon': 'islands#darkGreenStretchyIcon',
                'islands#redStretchyIcon': 'islands#redStretchyIcon',
                'islands#violetStretchyIcon': 'islands#violetStretchyIcon',
                'islands#darkOrangeStretchyIcon': 'islands#darkOrangeStretchyIcon',
                'islands#blackStretchyIcon': 'islands#blackStretchyIcon',
                'islands#nightStretchyIcon': 'islands#nightStretchyIcon',
                'islands#yellowStretchyIcon': 'islands#yellowStretchyIcon',
                'islands#darkBlueStretchyIcon': 'islands#darkBlueStretchyIcon',
                'islands#greenStretchyIcon': 'islands#greenStretchyIcon',
                'islands#pinkStretchyIcon': 'islands#pinkStretchyIcon',
                'islands#orangeStretchyIcon': 'islands#orangeStretchyIcon',
                'islands#grayStretchyIcon': 'islands#grayStretchyIcon',
                'islands#lightBlueStretchyIcon': 'islands#lightBlueStretchyIcon',
                'islands#brownStretchyIcon': 'islands#brownStretchyIcon',
                'islands#oliveStretchyIcon': 'islands#oliveStretchyIcon'
            };

            var defaults = {
                "center": {
                    lat: 55.76,
                    lng: 37.64
                },
                placemark: {
                    style: 'islands#redIcon',
                    content: '',
                    position: {
                        lat: 55.76,
                        lng: 37.64
                    }
                },
                "zoom": 4,
                "type": 'yandex#map',
                "controls": {
                    "zoom_control": true,
                    "search_control": false,
                    "ruler_control": false,
                    "traffic_control": false,
                    "type_selector": true
                }
            };

            $.extend(defaults,$scope.config);

            function init() {
                var placemark_draggable = true;

                var map = init_map($element, $scope.config);
                var placemark = init_placemark($scope.config.placemark, placemark_draggable);
                map.geoObjects.add(placemark);

                function trigger_change(name, value)
                {
                    var $event = {};
                    if (arguments.length == 2) {
                        $event.changes = {};
                        $event.changes[name] = value;
                    } else if (arguments.length == 1 && angular.isObject(name)) {
                        $event.changes = name;
                    }

                    $scope.$parent.$eval($attrs.onChange, {$event: $event});
                }

                function on_type_selector_select() {
                    var type = map.getType();

                    if ($scope.config.type !== type)
                        $scope.$apply(function () {
                            $scope.config.type = type;
                            trigger_change('type', type)
                        });
                }

                function bind_type_selector_events()
                {
                    var typeSelector = map.controls.get('typeSelector');
                    if (typeSelector) {
                        typeSelector.events.add('select', on_type_selector_select)
                    }
                }
                bind_type_selector_events();

                $scope.mapTypeSelectorChange = function () {
                    var state = $scope.config.controls.type_selector;

                    map.controls[state ? 'add' : 'remove']('typeSelector');
                    trigger_change('type_selector', state);
                    bind_type_selector_events();
                };

                $scope.mapControlChange = function (name) {
                    var state = $scope.config.controls[name];

                    map.controls[state ? 'add' : 'remove'](
                        Tools.underscoreToCamelCase(name)
                    );
                    trigger_change(name, state)
                };

                $scope.mapZoomChange = function () {
                    map.setZoom($scope.config.zoom);
                    trigger_change('zoom', $scope.config.zoom)
                };

                $scope.mapCenterChange = function () {
                    map.setCenter([$scope.config.center.lat, $scope.config.center.lng]);
                    trigger_change({
                        center_lat: $scope.config.center.lat,
                        center_lng: $scope.config.center.lng
                    });
                };

                $scope.placemarkPositionChange = function () {
                    placemark.geometry.setCoordinates([
                        $scope.config.placemark.position.lat,
                        $scope.config.placemark.position.lng
                    ])
                };

                $scope.mapStyleChange = function () {
                    map.setType($scope.config.type);
                    trigger_change('type', $scope.config.type);
                };

                $scope.placemarkStyleChange = function () {
                    placemark.options.set('preset', $scope.config.placemark.style);
                    trigger_change('placemark_style', $scope.config.placemark.style);
                };

                $scope.placemarkContentChange = function () {
                    placemark.properties.set('iconContent', $scope.config.placemark.content);
                    timeout_queue.add('placemark_content', $timeout(function () {
                        trigger_change('placemark_content', $scope.config.placemark.content);
                    }, 1500));
                };

                if (placemark_draggable)
                {
                    placemark.events.add('dragend', function () {
                        var coords = placemark.geometry.getCoordinates();
                        $scope.$apply(function () {
                            $scope.config.placemark.position.lat = coords[0];
                            $scope.config.placemark.position.lng = coords[1];
                            trigger_change({
                                placemark_lat: $scope.config.placemark.position.lat,
                                placemark_lng: $scope.config.placemark.position.lng
                            });
                        });

                    });
                }
                else
                {
                    map.events.add('click', function (e) {
                        var coords = e.get('coords');
                        $scope.$apply(function () {
                            placemark.geometry.setCoordinates(coords);
                            $scope.config.placemark.position.lat = coords[0];
                            $scope.config.placemark.position.lng = coords[1];
                            trigger_change({
                                placemark_lat: $scope.config.placemark.position.lat,
                                placemark_lng: $scope.config.placemark.position.lng
                            });
                        });
                    });
                }

                map.events.add('boundschange', function (e) {

                    var old_center = e.get('oldCenter');
                    var old_lat = old_center[0];
                    var old_lng = old_center[1];

                    var new_center = e.get('newCenter');
                    var new_lat = new_center[0];
                    var new_lng = new_center[1];

                    var zoom_changed = e.get('newZoom') != e.get('oldZoom');
                    var center_changed = old_lat !== new_lat || old_lng !== new_lng;

                    if (zoom_changed || center_changed) {

                        $scope.$apply(function () {

                            var changes = {};
                            if (zoom_changed)
                                changes.zoom = $scope.config.zoom  = e.get('newZoom');

                            if (center_changed)
                            {
                                changes.center_lat = $scope.config.center.lat = new_lat;
                                changes.center_lng = $scope.config.center.lng = new_lng;
                            }

                            trigger_change(changes);
                        });
                    }
                });

                $element.show();
            }

            load_api().then(function () {
                ymaps.ready(init);
            });
        }

        return {
            restrict: 'E',
            templateUrl: 'components/maps/directives/map-configurator.tpl',
            scope: {
                config: '=configuration'
            },
            link: link
        }
    }
})();