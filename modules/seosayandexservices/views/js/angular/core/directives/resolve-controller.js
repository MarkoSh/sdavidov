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
    angular.module('sya.core')
        .directive('resolveController', ResolveControllerDirective);

    ResolveControllerDirective.$inject = ['$injector', '$controller', '$q', '$compile'];
    function ResolveControllerDirective ($injector, $controller, $q, $compile) {
        return {
            restrict: 'A',
            scope: true,
            compile: function($element) {
                // Break the recursion loop by removing the contents
                var contents = $element.contents().remove();
                var compiledContents;
                return {
                    pre: null,
                    post: function ($scope, $element, $attrs) {
                        $element.hide();
                        var injections = {$scope: $scope};
                        var instantiate = $controller($attrs.resolveController, injections, true);
                        var constructor = instantiate.instance.constructor;

                        var resolved = function () {
                            if (!compiledContents) {
                                compiledContents = $compile(contents);
                            }
                            $element.show();
                            instantiate();
                            compiledContents($scope, function(clone) {
                                $element.append(clone);
                            });
                        };

                        if (!constructor.$resolve)
                            return resolved();

                        var promises = {};
                        for (var key in constructor.$resolve) {
                            if (!constructor.$resolve.hasOwnProperty(key)) continue;
                            var resolver = constructor.$resolve[key];
                            promises[key] = $injector.invoke(resolver);
                        }

                        $q.all(promises).then(function (data) {
                            angular.extend(injections, data);
                            return resolved();
                        }, function () {
                            $injector.get('$rootScope').$broadcast('stateChangeError')
                        });
                    }
                };
            },
            priority: 500
        }
    }
})();