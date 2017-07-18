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
    angular.module('sya.share')
        .directive('shareShareConfigurator', ShareConfiguratorDirective);


    ShareConfiguratorDirective.$inject = ['$q', 'Tools', 'PromiseQueue', '$timeout', 'current_language_iso_code', 'site_logo_image_url'];
    function ShareConfiguratorDirective($q, Tools, PromiseQueue, $timeout, current_language_iso_code, site_logo_image_url) {

        function load_api() {
            if (!load_api.promise) {
                var deferred = $q.defer();

                $.getScript('http://yastatic.net/share/share.js', deferred.resolve);

                load_api.promise = deferred.promise
            }

            return load_api.promise
        }


        function link($scope, $element, $attrs) {
            var timeout_queue = new PromiseQueue($timeout);


            $scope.styles = [
                {name: 'small', label: 'Counters'},
                {name: 'button', label: 'Button'},
                {name: 'link', label: 'Link'},
                {name: 'icon', label: 'Icons an menu'},
                {name: 'none', label: 'Only icons'}
            ];

            $scope.themes = [
                {name: 'default', label: 'Default'},
                {name: 'dark', label: 'Dark'},
                {name: 'counter', label: 'Counters'}
            ];

            $scope.directions = [
                {value: undefined, label: 'Auto'},
                {value: 'up', label: 'Up'},
                {value: 'down', label: 'Down'}
            ];

            $scope.socials = {
                'vkontakte': {name: "vkontakte", label: "ВКонтакте"},
                'odnoklassniki': {name: "odnoklassniki", label: "Одноклассники.ru"},
                'twitter': {name: "twitter", label: "Twitter"},
                'facebook': {name: "facebook", label: "facebook"},
                'gplus': {name: "gplus", label: "Google+"},
                'moimir': {name: "moimir", label: "Мой Мир"},
                'lj': {name: "lj", label: "Live Journal"},
                'pinterest': {name: "pinterest", label: "Pinterest", requireImage: true},
                'liveinternet': {name: "liveinternet", label: "LiveInternet"},
                'blogger': {name: "blogger", label: "Blogger"},
                'delicious': {name: "delicious", label: "delicious"},
                'digg': {name: "digg", label: "Digg"},
                'evernote': {name: "evernote", label: "Evernote"},
                'friendfeed': {name: "friendfeed", label: "FriendFeed"},
                'juick': {name: "juick", label: "Juick"},
                'linkedin': {name: "linkedin", label: "LinkedIn"},
                'moikrug': {name: "moikrug", label: "Мой Круг"},
                'myspace': {name: "myspace", label: "MySpace"},
                'surfingbird': {name: "surfingbird", label: "Surfingbird"},
            };


            $scope.config = $scope.config || {};
            $scope.config.popup_blocks = $scope.config.popup_blocks || [];
            $scope.config.main_block     = $scope.config.main_block || [];

            var restore_popup_block_max = $scope.config.popup_blocks.length;
            var restore_popup_states = [];

            function trigger_change()
            {
                $scope.$parent.$eval($attrs.onChange);
            }

            function sync_config_with_droparea(area) {
                var array = [];
                var ngModel = area.data('$ngModelController');
                area.find('li:not(.hint)').each(function () {
                    array.push($(this).data('social'));
                });

                $scope.$apply(function () {
                    ngModel.$setViewValue(array);
                });
            }

            function build_small_icon(social) {
                return '<li title="' + social.label + '" data-social="' + social.name + '">' +
                    '<span class="icon icon-social icon-social-' + social.name + '"></span>' +
                    '</li>';
            }

            function convert_config(element, config) {
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
                    element: element,
                    l10n: current_language_iso_code || 'ru',
                    theme: config.theme || 'default',
                    elementStyle: {
                        'type': config.style || 'none',
                        'border': config.border || false,
                        'linkUnderline': config.linkUnderline || false,
                        'linkIcon': config.linkIcon || false,
                        'quickServices': config.main_block || ['', 'vkontakte', 'twitter', 'facebook']
                    },
                    link: 'http://www.yandex.com/',
                    title: 'Yandex — the best search engine in the universe!',
                    image: site_logo_image_url,
                    popupStyle: popupStyle
                };
            }

            $scope.buildShareElement = function (no_trigger) {
                var element = $element.find('share');
                if (!element.length)
                    return;
                element.before('<share></share>');
                element.remove();

                var ya_config = convert_config($element.find('share').get(0), $scope.config);

                function init() {
                    new Ya.share(ya_config);
                }

                load_api().then(init);
                if (!no_trigger)
                    trigger_change();
            }

            $scope.buildShareElementDelayed = function () {
                var key = 'buildShareElementDelayed';

                timeout_queue.add(key, $timeout($scope.buildShareElement, 1000));
            }

            $scope.restorePopupBlock = function ($index, $event) {
                if ($index < restore_popup_block_max && restore_popup_states.indexOf($index) == -1)
                {
                    var popup_block = $scope.config.popup_blocks[$index];

                    if (popup_block && popup_block.socials && popup_block.socials.length)
                    {
                        for (var i = 0; i < popup_block.socials.length; i++)
                        {
                            var social = $scope.socials[popup_block.socials[i]];
                            $event.target.append(build_small_icon(social))
                        }
                    }

                    restore_popup_states.push($index)
                    $scope.buildShareElement(true);
                }
            }

            $scope.restoreMainBlock = function ($event) {
                for (var i = 0; i < $scope.config.main_block.length; i++)
                {
                    var social = $scope.socials[$scope.config.main_block[i]];
                    $event.target.append(build_small_icon(social))
                }

                $scope.restoreMainBlock = angular.noop;
                $scope.buildShareElement(true);
            }

            $scope.dnd = {
                droppable: {
                    accept: '[data-social]',
                    activeClass: "trash-droparea-active",
                    hoverClass: "trash-droparea-hover",
                    drop: function (e, ui) {
                        ui.draggable.remove();
                    }
                },
                draggable: {
                    helper: function (event, ui) {
                        var social = $(event.target).scope().social
                        return build_small_icon(social);
                    },
                    connectToSortable: "[sya-sortable]",
                },
                sortable: {
                    helper: "clone",
                    items: "li",
                    connectWith: "li",
                    cancel: "li.hint",
                    placeholder: "sortable-placeholder",
                    receive: function (e, ui) {
                        var social = ui.item.scope().social;
                        var newItem = $(this).data().uiSortable.currentItem;
                        newItem.replaceWith(build_small_icon(social));
                    },
                    stop: function (e, ui) {
                        sync_config_with_droparea($(this));
                    }
                }
            };

            $scope.addPopupBlock = function () {
                $scope.config = $scope.config || {};
                $scope.config.popup_blocks = $scope.config.popup_blocks || [];
                $scope.config.popup_blocks.push({
                    title: '',
                    socials: [],
                })
            }

            $scope.removePopupBlock = function (index) {
                $scope.config = $scope.config || {};
                $scope.config.popup_blocks = $scope.config.popup_blocks || [];
                $scope.config.popup_blocks.splice(index, 1);
                $scope.buildShareElement();
            }
        }

        return {
            restrict: 'E',
            templateUrl: 'components/share/directives/share-configurator.tpl',
            scope: {
                config: '=configuration'
            },
            link: link
        }
    }
})();