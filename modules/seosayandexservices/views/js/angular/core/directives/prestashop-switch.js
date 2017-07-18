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
        .directive('prestashopSwitch', PrestashopSwitchDirective);

    PrestashopSwitchDirective._idCounter = 0;
    PrestashopSwitchDirective.$inject = [];
    function PrestashopSwitchDirective () {

        var link = function ($scope, $element, $attr, ngModel) {
            $scope.id = $attr.id || 'prestashop-switch'+ (++PrestashopSwitchDirective._idCounter);
            $scope.name = $attr.name || $scope.id;
            $scope.trueValue = $attr.trueValue ? $scope.$parent.$eval($attr.trueValue) : true;
            $scope.falseValue = $attr.falseValue ? $scope.$parent.$eval($attr.falseValue) : false;
            $scope.onText = $attr.onText || 'Yes';
            $scope.offText = $attr.offText  || 'No';

            $scope.tmpValue = ngModel.$modelValue;

            var $$render = ngModel.$render;
            ngModel.$render = function () {
                if ($$render)
                    $$render.apply(this, arguments);

                $scope.tmpValue = ngModel.$viewValue;
            };

            $scope.sync = function () {
                ngModel.$setViewValue($scope.tmpValue);
            };

            $scope.$watch(function () {
                return !!$element.attr('disabled');
            }, function (disabled) {
                $scope.disabled = disabled;
                if (!disabled && ngModel.$viewValue !== $scope.trueValue && ngModel.$viewValue !== $scope.falseValue)
                    $scope.tmpValue = $scope.trueValue;
            });
        };

        return {
            require: 'ngModel',
            restrict: 'AE',
            link: link,
            scope: {},
            templateUrl: 'core/prestashop-switch.tpl'
        }
    }
})();