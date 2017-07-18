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
    angular.module('sya.lazy-load').directive('lazyLoad', LazyLoadDirective);

    LazyLoadDirective.$inject = ['LazyLoader', '$http', '$templateCache', '$compile'];
    function LazyLoadDirective(LazyLoader, $http, $templateCache, $compile) {
        return {
            compile: function ($element, $attrs) {
                var html = $element.html();
                $element.empty();

                return function ($scope, $element, $attrs) {
                    var requiredModuleExpression = $attrs.lazyLoad;

                    var childScope;

                    function clearContent() {
                        if (childScope) {
                            childScope.$destroy();
                            childScope = null;
                        }
                        $element.html('');
                    }

                    function load_template(url) {
                        return $http.get(url, {cache: $templateCache});
                    }

                    function on_template_load(template) {

                        childScope = $scope.$new();
                        $element.html(template);

                        var content = $element.contents(),
                            linkFn = $compile(content);

                        linkFn(childScope);
                    }

                    $scope.$watch(requiredModuleExpression, function(moduleName) {
                        if (!moduleName) return;

                        if (moduleName) {
                            LazyLoader.load(moduleName).then(function() {
                                var moduleConfig = LazyLoader.getConfig(moduleName);
                                if (moduleConfig.template)
                                    load_template(moduleConfig.template).then(on_template_load);
                                else
                                    on_template_load(html);
                            });
                        } else
                            clearContent();
                    });

                    $scope.$on('$destroy', clearContent);
                }

            }
        };
    }
})();