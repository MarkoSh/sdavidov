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
        .directive('modelNullable', FormGroupDirective);

    FormGroupDirective.$inject = ['$rootScope'];
    function FormGroupDirective ($rootScope) {

        function link ($scope, $element, $attrs, ngModel) {
            var wrapper = $element.closest('.input-group');
            if (wrapper.length === 0)
            {
                $element.wrap('<div class="input-group"></div>');
                wrapper = $element.closest('.input-group');
                $scope.$on('$destroy', wrapper.remove);
            }

            var check_box_wrapper =$('<span class="input-group-addon"></span>');
            var check_box =$('<input type="checkbox">');
            check_box_wrapper.append(check_box);
            wrapper.prepend(check_box_wrapper);

            var $$render = ngModel.$render;
            ngModel.$render = function () {
                if ($$render)
                    $$render.apply(this, arguments);

                if (null !== ngModel.$viewValue)
                    check_box.attr('checked', 'checked');
                else
                    $element.attr('disabled', 'true');
            };

            var has_previous_value = false, previous_value;
            check_box.on('change', function() {
                if (check_box.is(':checked'))
                {
                    $element.removeAttr('disabled');
                    if (has_previous_value)
                        ngModel.$setViewValue(previous_value);
                }
                else
                {
                    has_previous_value = true;
                    previous_value = ngModel.$viewValue;
                    ngModel.$setViewValue(null);
                    $element.attr('disabled', 'true');
                }

                $rootScope.$apply();
            });

            $scope.$on('$destroy', check_box_wrapper.remove);
        }

        return {
            require: 'ngModel',
            link: link
        }
    }
})();